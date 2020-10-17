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
    private float $step = 0.01; // Consistent with decimal
    private ?float $remainder = null;

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
     * f(N, Dv, Dc, S) with :
     *  - N the number
     *  - Dv the divider
     *  - Dc the number of decimals
     *  - S the step size.
     *
     * This function is described by this equations :
     * [
     *  - S = S || 10^(-Dc)
     *  - Dc = Dc || GetNumberOfDecimalOfStep(S)
     *
     *  - rm + p¹ + p² + ... p^n = N (where rm the reminder, p a part and n in range [1, Dv])
     *  - rm = N - p¹ - p² - ... p^n (same as above - rewriting order to lookup at reminder)
     *
     *  - Ns = ⌊N / S⌋ (the stepped Number) or the max number of steps
     *
     *  - dNs = ⌊Ns / Dv⌋ default steps number by parts
     *
     *  - p^n = S * x^n (where x is an integer and n in range [1, Dv]) This is the value of p¹ ... p² ...
     *
     *  - x^n = dNs + { 1 }IF[ (dNs * Dv + n) <= Ns) && (ALLOCATE === MODE) ]
     *
     *  fmod is a shit!!!
     *  php > echo fmod(100.1, 0.1);
     *  0.099999999999989
     *  Should be 0!
     *
     *  - rm according to the first definition above can now be extended to
     *    - rm = N - Ns * S for ALLOCATE
     *    or
     *    - rm = N - dNs * Dv * S for EQUITY
     * ]
     *
     * And perform this algorithm :
     *  - Define S when S or Dc is set
     *  - Define Dc when Dc or S is set
     *
     *  On split or before doing split (these value can be recalculated each time that S or Dc change)
     *  TODO choice during implementation
     *
     *  - Define ns
     *  - Define dNs
     *
     *  - Generate generator for parts
     *
     *  - Define rm according "to mode (so, split must be called)"
     */
    public function split(string $mode = self::SPLIT_MODE_ALLOCATE): Generator
    {
        if (!\in_array($mode, self::SPLIT_MODES, true)) {
            throw new ResidueModeException(sprintf('Accepted modes are : %s', implode(', ', self::SPLIT_MODES)));
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

            yield ($this->isNegative ? -1 : 1) * $xn * $this->step;
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

        $this->decimal = $decimal;

        $this->step = 10 ** -$decimal;

        return $this;
    }

    public function getRemainder(): float
    {
        if (null === $this->remainder) {
            throw new CannotGetRemainderException('Split must have to be called before getting the remainder');
        }

        return $this->remainder;
    }

    public function toArray(string $mode = self::SPLIT_MODE_ALLOCATE): array
    {
        return iterator_to_array($this->split($mode), false);
    }

    protected function isNegative(float $value): bool
    {
        return $value < 0.0;
    }

    protected function getDecimalLength(float $float): int
    {
        $strFloat = (string) $float;
        if (false === mb_strpos($strFloat, '.')) {
            return 0;
        }

        return mb_strlen(explode('.', $strFloat)[1]);
    }

    protected function calculateRemainder(string $mode, float $maxNumberOfSteps, float $defaultNumberOfSteps): float
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
