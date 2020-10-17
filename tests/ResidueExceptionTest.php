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
use Romainnorberg\Residue\Exception\DivideException;
use Romainnorberg\Residue\Exception\ResidueModeException;
use Romainnorberg\Residue\Exception\StepException;
use Romainnorberg\Residue\Residue;

class ResidueExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_throw_residue_mode_exception(): void
    {
        $this->expectException(ResidueModeException::class);

        Residue::create(100)
            ->toArray('none existing mode')
        ;
    }

    /**
     * @test
     */
    public function it_should_throw_cannot_get_remainder_exception(): void
    {
        $this->expectException(CannotGetRemainderException::class);

        Residue::create(100)
            ->getRemainder()
        ;
    }

    /**
     * @test
     * @dataProvider provideDataForDivideException
     */
    public function it_should_throw_divide_exception(int $minusThanOne): void
    {
        $this->expectException(DivideException::class);

        Residue::create(100)
            ->divideBy($minusThanOne);
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

    public function provideDataForDivideException()
    {
        return [
            [0],
            [-1],
        ];
    }
}
