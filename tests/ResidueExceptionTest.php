<?php

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romainnorberg\Residue\Tests;

use PHPUnit\Framework\TestCase;
use Romainnorberg\Residue\Exception\CannotGetRemainderException;
use Romainnorberg\Residue\Exception\DecimalException;
use Romainnorberg\Residue\Exception\DivideByZeroException;
use Romainnorberg\Residue\Exception\StepException;
use Romainnorberg\Residue\Residue;

class ResidueExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_throw_remainder_exception(): void
    {
        $this->expectException(CannotGetRemainderException::class);

        $residue = Residue::create(7.315)
            ->divideBy(3)
            ->decimal(3)
            ->step(0.05);

        $residue->getStepRemainder();
    }

    /**
     * @test
     */
    public function it_should_throw_divide_by_zero_exception(): void
    {
        $this->expectException(DivideByZeroException::class);

        Residue::create(100)
            ->divideBy(0);
    }

    /**
     * @test
     */
    public function it_should_throw_step_exception(): void
    {
        $this->expectException(StepException::class);

        Residue::create(100)
            ->divideBy(3)
            ->decimal(3)
            ->step(-1);
    }

    /**
     * @test
     */
    public function it_should_throw_decimal_exception(): void
    {
        $this->expectException(DecimalException::class);

        Residue::create(100)
            ->divideBy(3)
            ->decimal(-1);
    }
}
