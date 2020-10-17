<?php

declare(strict_types=1);

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romainnorberg\Residue;

use Generator;
use Romainnorberg\Residue\Contracts\ResidueInterface;
use Romainnorberg\Residue\Exception\CannotGetRemainderException;
use Romainnorberg\Residue\Exception\DecimalException;
use Romainnorberg\Residue\Exception\DivideException;
use Romainnorberg\Residue\Exception\ResidueModeException;
use Romainnorberg\Residue\Exception\StepException;

final class Residue implements ResidueInterface
{
    private float $value;
    private bool $isNegative;
    private int $divider = 1;
    private int $decimal = 2;
    private float $step;
    private float $remainder;

    public function __construct(float $value)
    {
        $this->isNegative = $this->isNegative($value);
        $this->value = abs($value);
    }

    public static function create(float $value): self
    {
        return new self($value);
    }

    /**
     * split represent the function `f(N, Dv, Dc, S, M)`
     * with :
     *  - `N` is the number given to Residue constructor
     *  - `Dv` the divider given to divideBy Residue method
     *  - `Dc` the number of decimals whom can be set through decimals method and as default value of `2`
     *  - `S` the step size whom can be set to through step method and as no default value
     *  - `M` the split mode, is one value of set [allocate, equity]
     *
     * This function is described by this set of equations :
     *  - rm + ∑pⁿ = N
     *  where : - `rm` is the remainder
     *          - `p` a part with n in the range [1, Dv]
     *
     *  - Ns = ⌊N / S⌋
     *  with : - `Ns` is the max number of steps
     *
     *  - DNs = ⌊Ns / Dv⌋
     *  with : - `DNs` is the default number of steps by parts (pⁿ)
     *
     *  - pⁿ = S * xⁿ
     *  where : - `xⁿ` is an integer with n in range [1, Dv]
     *
     *  - xⁿ = DNs + (((M ↔ allocate) ∧ (DNs * Dv + n <= Ns)) ⇒ 1) ∨ 0
     *
     *  According to previous definitions `rm` can be now extended to :
     *  - rm = ((M ↔ allocate) ⇒ N - Ns * S) ∨ ((M ↔ equity) ⇒ N - dNs * Dv * S)
     *
     * And perform this algorithm :
     *  - Defining Ns
     *  - Defining Dns
     *  - Defining rm according to `M`
     *  - Iterate [n as 1 ... Dv] : yield pⁿ
     */
    public function split(string $mode = self::SPLIT_MODE_ALLOCATE): Generator
    {
        if (!\in_array($mode, self::SPLIT_MODES, true)) {
            throw new ResidueModeException(sprintf('Accepted modes are : %s', implode(', ', self::SPLIT_MODES)));
        }

        if (!isset($this->step)) {
            $this->decimal($this->decimal);
        }

        $maxNumberOfSteps = floor($this->value / $this->step);
        $defaultNumberOfSteps = floor($maxNumberOfSteps / $this->divider);

        $this->remainder = ($this->isNegative ? -1 : 1)
            * $this->calculateRemainder($mode, $maxNumberOfSteps, $defaultNumberOfSteps)
        ;

        for ($i = 1; $i <= $this->divider; ++$i) {
            $xn = $defaultNumberOfSteps;
            if (self::SPLIT_MODE_ALLOCATE === $mode &&
                ($defaultNumberOfSteps * $this->divider + $i) <= $maxNumberOfSteps
            ) {
                ++$xn;
            }

            // Round, here, is just for php internal representation

            yield round(($this->isNegative ? -1 : 1) * $xn * $this->step, $this->decimal);
        }
    }

    public function divideBy(int $divider): self
    {
        if (1 > $divider) {
            throw new DivideException('Dividing by less than one has no meaning');
        }
        $this->divider = $divider;

        return $this;
    }

    public function step(float $step): self
    {
        if ($step < 0) {
            throw new StepException('Step value must be positive');
        }

        $this->step = $step;

        $this->decimal = $this->getDecimalLength($step);

        return $this;
    }

    public function decimal(int $decimal): self
    {
        if ($decimal < 0) {
            throw new DecimalException('Decimal round value must be positive');
        }

        $stepCandidate = 10 ** -$decimal;

        if (!isset($this->step) || $stepCandidate > $this->step) {
            $this->decimal = $decimal;
            $this->step = $stepCandidate;
        }

        return $this;
    }

    public function getRemainder(): float
    {
        if (!isset($this->remainder)) {
            throw new CannotGetRemainderException('You should iterate over `split` method or call `toArray` before getting the remainder');
        }

        return $this->remainder;
    }

    public function toArray(string $mode = self::SPLIT_MODE_ALLOCATE): array
    {
        return iterator_to_array($this->split($mode), false);
    }

    private function isNegative(float $value): bool
    {
        return $value < 0.0;
    }

    private function getDecimalLength(float $float): int
    {
        $strFloat = (string) $float;
        if (false === mb_strpos($strFloat, '.')) {
            return 0;
        }

        return mb_strlen(explode('.', $strFloat)[1]);
    }

    private function calculateRemainder(string $mode, float $maxNumberOfSteps, float $defaultNumberOfSteps): float
    {
        $decimalLengthOfValue = $this->getDecimalLength($this->value);
        $remainderDecimalLength = $this->decimal < $decimalLengthOfValue ? $decimalLengthOfValue : $this->decimal;

        // Round, here, is just for php internal representation

        if (self::SPLIT_MODE_ALLOCATE === $mode) {
            return round($this->value - $maxNumberOfSteps * $this->step, $remainderDecimalLength);
        }

        return round(
            $this->value - $defaultNumberOfSteps * $this->divider * $this->step,
            $remainderDecimalLength
        );
    }
}
