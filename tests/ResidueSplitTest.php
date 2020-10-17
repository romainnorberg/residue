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
//    /**
//     * @test
//     * @dataProvider dataProviderValues
//     */
//    public function it_should_split(
//        float $value,
//        int $divider,
//        ?int $decimalValue,
//        array $expected
//    ): void {
//        $split = (new Residue($value))
//            ->divideBy($divider);
//
//        if (null !== $decimalValue) {
//            $split->decimal($decimalValue);
//        }
//
//        $this->assertEquals($expected, $split->toArray());
//    }
//
//    /**
//     * @test
//     * @dataProvider dataProviderValues
//     */
//    public function it_should_split_with_new_split(
//        float $value,
//        int $divider,
//        ?int $decimalValue,
//        array $expected
//    ): void {
//        $split = Residue::create($value)
//            ->divideBy($divider);
//
//        if (null !== $decimalValue) {
//            $split->decimal($decimalValue);
//        }
//
//        $this->assertEquals($expected, $split->toArray());
//    }
//
//    /**
//     * @test
//     * @dataProvider dataProviderValues
//     */
//    public function it_should_split_with_static_constructor(
//        float $value,
//        int $divider,
//        ?int $decimalValue,
//        array $expected
//    ): void {
//        $split = Residue::create($value)
//            ->divideBy($divider);
//
//        if (null !== $decimalValue) {
//            $split->decimal($decimalValue);
//        }
//
//        $this->assertEquals($expected, $split->toArray());
//    }
//
//    public function dataProviderValues()
//    {
//        yield '101/3' => [
//            101, // value
//            3, // divide
//            2, // decimal value
//            [
//                33.67,
//                33.67,
//                33.66,
//            ], // expected result
//        ];
//
//        yield '100/3' => [
//            100, // value
//            3, // divide
//            2, // decimal value
//            [
//                33.33,
//                33.33,
//                33.34,
//            ], // expected result
//        ];
//
//        yield '99.99/2' => [
//            99.99, // value
//            2, // divide
//            2, // decimal value
//            [
//                50.00,
//                49.99,
//            ], // expected result
//        ];
//
//        yield '99.99/3' => [
//            99.99, // value
//            3, // divide
//            2, // decimal value
//            [
//                33.33,
//                33.33,
//                33.33,
//            ], // expected result
//        ];
//
//        yield '99.99/4' => [
//            99.99, // value
//            4, // divide
//            2, // decimal value
//            [
//                25.00,
//                25.00,
//                25.00,
//                24.99,
//            ], // expected result
//        ];
//
//        yield '0.02/4' => [
//            0.02, // value
//            4, // divide
//            null, // decimal value -> default to 2
//            [
//                0.01,
//                0.01,
//                0.0,
//                0.0,
//            ], // expected result
//        ];
//
//        yield '100.123/4' => [
//            100.123, // value
//            4, // divide
//            3, // decimal value
//            [
//                25.031,
//                25.031,
//                25.031,
//                25.030,
//            ], // expected result
//        ];
//
//        yield '-100/3' => [
//            -100, // value
//            3, // divide
//            2, // decimal value
//            [
//                -33.33,
//                -33.33,
//                -33.34,
//            ], // expected result
//        ];
//
//        yield '-99.99/2' => [
//            -99.99, // value
//            2, // divide
//            2, // decimal value
//            [
//                -50.00,
//                -49.99,
//            ], // expected result
//        ];
//
//        yield '-99.99/3' => [
//            -99.99, // value
//            3, // divide
//            2, // decimal value
//            [
//                -33.33,
//                -33.33,
//                -33.33,
//            ], // expected result
//        ];
//
//        yield '-99.99/4' => [
//            -99.99, // value
//            4, // divide
//            2, // decimal value
//            [
//                -25.00,
//                -25.00,
//                -25.00,
//                -24.99,
//            ], // expected result
//        ];
//
//        yield '-0.02/4' => [
//            -0.02, // value
//            4, // divide
//            null, // decimal value -> default to 2
//            [
//                -0.01,
//                -0.01,
//                -0.0,
//                -0.0,
//            ], // expected result
//        ];
//
//        yield '-100.123/4' => [
//            -100.123, // value
//            4, // divide
//            3, // decimal value
//            [
//                -25.031,
//                -25.031,
//                -25.031,
//                -25.030,
//            ], // expected result
//        ];
//    }

    /**
     * @test
     * @dataProvider dataProviderAllocateValues
     */
    public function it_should_split_with_mode_allocate(
        float $value,
        int $divider,
        ?int $decimalValue,
        ?float $step,
        array $expected,
        float $expectedremainder
    ): void {
        $residue = Residue::create($value)->divideBy($divider);

        if (null !== $step) {
            $residue->step($step);
        }

        if (null !== $decimalValue) {
            $residue->decimal($decimalValue);
        }

        $this->assertEquals($expected, $residue->toArray());

        $this->assertEquals($expectedremainder, $residue->getRemainder());
    }

    public function dataProviderAllocateValues()
    {
        yield '1/1 - Step 0.0' => [
            1, // value
            1, // divide
            null, // decimal value
            null, // step
            [
                1,
            ], // expected result
            0, // reminder
        ];

        yield '5/2 - Step 0.0' => [
            5, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                2.5,
                2.5,
            ], // expected result
            0, // reminder
        ];

        yield '11.11/2 - Step 0.0' => [
            11.11, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                5.56,
                5.55,
            ], // expected result
            0.0, // reminder
        ];

        yield '11.11/2 - Step 0.1' => [
            11.11, // value
            2, // divide
            null, // decimal value
            0.1, // step
            [
                5.6,
                5.5,
            ], // expected result
            0.01, // reminder
        ];

        yield '23,3332/3 - Step 0.0' => [
            23.3332, // value
            3, // divide
            3, // decimal value
            null, // step
            [
                7.778,
                7.778,
                7.777,
            ], // expected result
            0.0002, // reminder
        ];

        yield '100/3 - Step 0.05' => [
            100, // value
            3, // divide
            null, // decimal value
            0.05, // step
            [
                33.35,
                33.35,
                33.30,
            ], // expected result
            0, // reminder
        ];

        yield '100.2/3 - Step 0.1' => [
            100.2, // value
            3, // divide
            null, // decimal value
            0.1, // step
            [
                33.4,
                33.4,
                33.4,
            ], // expected result
            0, // reminder
        ];

        yield '100.21/3 - Step 0.1' => [
            100.21, // value
            3, // divide
            null, // decimal value
            0.1, // step
            [
                33.4,
                33.4,
                33.4,
            ], // expected result
            0.01, // reminder
        ];

        yield '100.31/3 - Step 0.1' => [
            100.31, // value
            3, // divide
            null, // decimal value
            0.1, // step
            [
                33.5,
                33.4,
                33.4,
            ], // expected result
            0.01, // reminder
        ];

        yield '2.9/2 - Step 0.1' => [
            2.9, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.44,
                1.44,
            ], // expected result
            0.02, // reminder
        ];

        yield '2.91/2 - Step 0.1' => [
            2.91, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.47,
                1.44,
            ], // expected result
            0, // reminder
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderEquityValues
     */
    public function it_should_split_with_mode_equity(
        float $value,
        int $divider,
        ?int $decimalValue,
        ?float $step,
        array $expected,
        float $expectedremainder
    ): void {
        $residue = Residue::create($value)->divideBy($divider);

        if (null !== $step) {
            $residue->step($step);
        }

        if (null !== $decimalValue) {
            $residue->decimal($decimalValue);
        }

        $this->assertEquals($expected, $residue->toArray(Residue::SPLIT_MODE_EQUITY));

        $this->assertEquals($expectedremainder, $residue->getRemainder());
    }

    public function dataProviderEquityValues()
    {
        yield '1/1 - Step 0.0' => [
            1, // value
            1, // divide
            null, // decimal value
            null, // step
            [
                1,
            ], // expected result
            0, // reminder
        ];

        yield '100/3 - Step 0.0' => [
            100, // value
            3, // divide
            0, // decimal value
            null, // step
            [
                33,
                33,
                33,
            ], // expected result
            1, // reminder
        ];

        yield '100/3 - Step 0.01' => [
            100, // value
            3, // divide
            2, // decimal value
            null, // step
            [
                33.33,
                33.33,
                33.33,
            ], // expected result
            0.01, // reminder
        ];

        yield '2.91/2 - Step 0.1' => [
            2.91, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.44,
                1.44,
            ], // expected result
            0.03, // reminder
        ];
    }

    /**
     * @test
     */
    public function it_should_split_test_case(): void
    {
        // work as expected
        $residue = Residue::create(7.315)
            ->divideBy(3)
            ->decimal(3)
            ->step(0.050);

        $this->assertEquals([2.45, 2.45, 2.4], $residue->toArray());
        $this->assertEquals(0.015, $residue->getRemainder());

        // not fail anymore
        $residue = Residue::create(7.315)
            ->divideBy(3)
            ->step(0.050) // <-- step before decimal
            ->decimal(3);

        $this->assertEquals([2.45, 2.45, 2.4], $residue->toArray());
        $this->assertEquals(0.015, $residue->getRemainder());

        // work as expected
        $residue = Residue::create(7.315)
            ->divideBy(3)
            ->step(0.050) // <-- step before decimal
            ->decimal(1);

        //One should expect a result of this type :

        $this->assertEquals([2.5, 2.4, 2.4], $residue->toArray());
        $this->assertEquals(0.015, $residue->getRemainder());
    }
}
