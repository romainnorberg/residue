<?php

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romainnorberg\Residue;

use Generator;
use InvalidArgumentException;
use Romainnorberg\Residue\Exception\CannotGetRemainderException;

final class Residue
{
    private $value;
    private int $divider;
    private int $decimal = 2;
    private bool $isNegative;
    private float $step = 0.0;
    private ?float $stepRemainder;

    public function __construct(float $value)
    {
        $this->isNegative = $this->isNegative($value);
        $this->value = abs($value);
    }

    public static function create(float $value): self
    {
        return new self($value);
    }

    public function divideBy(int $divider): self
    {
        $this->divider = $divider;

        return $this;
    }

    public function step(float $step): self
    {
        if ($step < 0) {
            throw new InvalidArgumentException('Step value must be positive');
        }

        $this->step = $step;

        return $this;
    }

    public function decimal(int $decimal): self
    {
        if ($decimal < 0) {
            throw new InvalidArgumentException('Decimal round value must be positive');
        }

        $this->decimal = $decimal;

        return $this;
    }

    public function split(): Generator
    {
        if (!$this->hasStep()) {
            return $this->splitWithoutStep();
        }

        return $this->splitWithStep();
    }

    /**
     * Gives the limit at which the total of the result of the division has already been fully returned.
     */
    public function obstacle(): int
    {
        $obstacle = 0;
        $total = 0.00;
        while ($total < $this->value) {
            $total += round($this->value / $this->divider, $this->decimal);

            ++$obstacle;
        }

        return $obstacle;
    }

    public function remainder(): float
    {
        return $this->value - ($this->part() * $this->divider);
    }

    public function part(): float
    {
        return round($this->value / $this->divider, $this->decimal);
    }

    public function toArray(): array
    {
        return iterator_to_array($this->split(), false);
    }

    public function isNegative(float $value): bool
    {
        return $value < 0.0;
    }

    public function hasStep(): bool
    {
        return 0.0 !== $this->step;
    }

    public function splitWithoutStep(): Generator
    {
        $yields = range(0, $this->divider - 1);
        $part = $this->part();
        $obstacle = $this->obstacle();

        foreach ($yields as $key) {
            if ($obstacle <= $key) {
                yield 0.0;

                continue;
            }

            if (!next($yields)) {
                $yield = round($part + $this->remainder(), $this->decimal);

                yield $this->isNegative ? -$yield : $yield;

                break;
            }

            yield $this->isNegative ? -$part : $part;
        }
    }

    public function splitWithStep(): Generator
    {
        $dividers = range(0, $this->divider - 1);
        $value = $this->value / $this->divider;
        $this->stepRemainder = 0.0;

        foreach ($dividers as $part) {
            $modulo = fmod($value, $this->step);

            $this->stepRemainder += $modulo;
            $parts[] = $value - $modulo;
        }

        foreach ($dividers as $key => $part) {
            $yield = $parts[$key] ?? $part;

            if (round($this->stepRemainder, $this->decimal) >= round($this->step, $this->decimal)) {
                $yield += $this->step;

                $this->stepRemainder -= $this->step;
            }

            yield round($yield, $this->decimal);
        }
    }

    public function getStepRemainder(): ?float
    {
        if (!$this->hasStep()) {
            return $this->stepRemainder ?? null;
        }

        if (!isset($this->stepRemainder)) {
            throw new CannotGetRemainderException('You must iterate through the split() method or call toArray() method to be able to get the remaining value.');
        }

        return $this->stepRemainder;
    }
}
