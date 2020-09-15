<?php

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romainnorberg\Residue\Tests;

use PHPUnit\Framework\TestCase;
use Romainnorberg\Residue\Residue;

class ResidueSplitTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProviderPositiveValues
     */
    public function it_should_split_positive_values(
        float $value,
        int $divider,
        ?int $decimalValue,
        array $expected
    ): void
    {
        $split = (new Residue($value))
            ->divideBy($divider);

        if ($decimalValue !== null) {
            $split->decimal($decimalValue);
        }

        $this->assertEquals($expected, $split->toArray());
    }

    public function dataProviderPositiveValues()
    {
        yield '100/3' => [
            100, // value
            3, // divide
            2, // decimal value
            [
                33.33,
                33.33,
                33.34,
            ] // expected result
        ];

        yield '99.99/2' => [
            99.99, // value
            2, // divide
            2, // decimal value
            [
                50.00,
                49.99,
            ] // expected result
        ];

        yield '99.99/3' => [
            99.99, // value
            3, // divide
            2, // decimal value
            [
                33.33,
                33.33,
                33.33,
            ] // expected result
        ];

        yield '99.99/4' => [
            99.99, // value
            4, // divide
            2, // decimal value
            [
                25.00,
                25.00,
                25.00,
                24.99,
            ] // expected result
        ];

        yield '0.02/4' => [
            0.02, // value
            4, // divide
            null, // decimal value -> default to 2
            [
                0.01,
                0.01,
                0.0,
                0.0,
            ] // expected result
        ];

        yield '100.123/4' => [
            100.123, // value
            4, // divide
            3, // decimal value
            [
                25.031,
                25.031,
                25.031,
                25.030,
            ] // expected result
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderNegativeValues
     */
    public function it_should_split_negative_values(
        float $value,
        int $divider,
        ?int $decimalValue,
        array $expected
    ): void
    {
        $split = (new Residue($value))
            ->divideBy($divider);

        if ($decimalValue !== null) {
            $split->decimal($decimalValue);
        }

        $this->assertEquals($expected, $split->toArray());
    }

    public function dataProviderNegativeValues()
    {
        yield '-100/3' => [
            -100, // value
            3, // divide
            2, // decimal value
            [
                -33.33,
                -33.33,
                -33.34,
            ] // expected result
        ];

        yield '99.99/2' => [
            -99.99, // value
            2, // divide
            2, // decimal value
            [
                -50.00,
                -49.99,
            ] // expected result
        ];

        yield '99.99/3' => [
            -99.99, // value
            3, // divide
            2, // decimal value
            [
                -33.33,
                -33.33,
                -33.33,
            ] // expected result
        ];

        yield '99.99/4' => [
            -99.99, // value
            4, // divide
            2, // decimal value
            [
                -25.00,
                -25.00,
                -25.00,
                -24.99,
            ] // expected result
        ];

        yield '0.02/4' => [
            -0.02, // value
            4, // divide
            null, // decimal value -> default to 2
            [
                -0.01,
                -0.01,
                -0.0,
                -0.0,
            ] // expected result
        ];

        yield '100.123/4' => [
            -100.123, // value
            4, // divide
            3, // decimal value
            [
                -25.031,
                -25.031,
                -25.031,
                -25.030,
            ] // expected result
        ];
    }
    /**
     * @test
     * @dataProvider dataProviderStepValues
     */
    public function it_should_split_using_step(
        float $value,
        int $divider,
        ?int $decimalValue,
        float $step,
        array $expected,
        ?float $expectedStepRemainder = null
    ): void
    {
        $residue = (new Residue($value))
            ->divideBy($divider)
            ->step($step);

        if ($decimalValue !== null) {
            $residue->decimal($decimalValue);
        }

        $this->assertEquals($expected, $residue->toArray());

        if ($expectedStepRemainder !== null) {
            $this->assertEquals($expectedStepRemainder, $residue->getStepRemainder());
        }
    }

    public function dataProviderStepValues()
    {
        yield '100/3' => [
            100, // value
            3, // divide
            2, // decimal value
            0.05, // step
            [
                33.35,
                33.35,
                33.30,
            ] // expected result
        ];

        yield '0.15/4' => [
            0.15, // value
            4, // divide
            2, // decimal value
            0.05, // step
            [
                0.05,
                0.05,
                0.05,
                0.00,
            ] // expected result
        ];

        yield '12.25/2' => [
            12.25, // value
            2, // divide
            2, // decimal value
            0.05, // step
            [
                6.15,
                6.10,
            ] // expected result
        ];

        yield '7.315/3 - 0.005' => [
            7.315, // value
            3, // divide
            3, // decimal value
            0.005, // step
            [
                2.440,
                2.440,
                2.435,
            ] // expected result
        ];

        yield '7.315/3 - 0.05' => [
            7.315, // value
            3, // divide
            3, // decimal value
            0.05, // step
            [
                2.45,
                2.45,
                2.40,
            ], // expected result
            0.015 // expected remainder
        ];
    }
}
