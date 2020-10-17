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
    protected function it_should_split_with_mode(
        string $mode,
        float $value,
        int $divider,
        ?int $decimalValue,
        ?float $step,
        array $expected,
        float $expectedRemainder
    ): void {
        $this->assertEquals(
            $value,
            array_sum($expected) + $expectedRemainder
            , 'Something goes wrong in your test data set, value have to be equal to the sum of expected values + remainder as is: value = SUM($expected) + $expectedRemainder). Check your data set.'
        );

        $residue = Residue::create($value)->divideBy($divider);

        if (null !== $step) {
            $residue->step($step);
        }

        if (null !== $decimalValue) {
            $residue->decimal($decimalValue);
        }

        $split = $residue->toArray($mode);
        $remainder = $residue->getRemainder();

        $this->assertEquals($expected, $split);

        $this->assertEquals($expectedRemainder, $remainder);

        $this->assertEquals($value, array_sum($split) + $remainder);
    }

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
        float $expectedRemainder
    ): void {
        $this->it_should_split_with_mode(
            Residue::SPLIT_MODE_ALLOCATE,
            $value,
            $divider,
            $decimalValue,
            $step,
            $expected,
            $expectedRemainder
        );
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
        float $expectedRemainder
    ): void {
        $this->it_should_split_with_mode(
            Residue::SPLIT_MODE_EQUITY,
            $value,
            $divider,
            $decimalValue,
            $step,
            $expected,
            $expectedRemainder
        );
    }

    public function dataProviderAllocateValues()
    {
        yield '-4/3 - Step 0.01' => [
            -4, // value
            3, // divide
            null, // decimal value
            null, // step
            [
                -1.34,
                -1.33,
                -1.33,
            ], // expected result
            0, // remainder
        ];

        yield '1/1 - Step 0.01' => [
            1, // value
            1, // divide
            null, // decimal value
            null, // step
            [
                1,
            ], // expected result
            0, // remainder
        ];

        yield '5/2 - Step 0.01' => [
            5, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                2.5,
                2.5,
            ], // expected result
            0, // remainder
        ];

        yield '11.11/2 - Step 0.01' => [
            11.11, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                5.56,
                5.55,
            ], // expected result
            0.0, // remainder
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
            0.01, // remainder
        ];

        yield '23,3332/3 - Step 0.001' => [
            23.3332, // value
            3, // divide
            3, // decimal value
            null, // step
            [
                7.778,
                7.778,
                7.777,
            ], // expected result
            0.0002, // remainder
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
            0, // remainder
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
            0, // remainder
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
            0.01, // remainder
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
            0.01, // remainder
        ];

        yield '2.9/2 - Step 0.03' => [
            2.9, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.44,
                1.44,
            ], // expected result
            0.02, // remainder
        ];

        yield '2.91/2 - Step 0.03' => [
            2.91, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.47,
                1.44,
            ], // expected result
            0, // remainder
        ];

        yield '5099/7 - Step 0.11' => [
            5099, // value
            7, // divide
            null, // decimal value
            0.11, // step
            [
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
            ], // expected result
            0.06, // remainder
        ];

        yield '5099.17/7 - Step 0.11' => [
            5099.17, // value
            7, // divide
            null, // decimal value
            0.11, // step
            [
                728.53,
                728.53,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
            ], // expected result
            0.01, // remainder
        ];

        yield '7.315/3 - Step 0.05' => [
            7.315, // value
            3, // divide
            3, // decimal value
            0.05, // step
            [
                2.45,
                2.45,
                2.40,
            ], // expected result
            0.015, // remainder
        ];

        yield '7.315/3 - Step 0.1' => [
            7.315, // value
            3, // divide
            1, // decimal value
            0.05, // step
            [
                2.5,
                2.4,
                2.4,
            ], // expected result
            0.015, // remainder
        ];
    }

    public function dataProviderEquityValues()
    {
        yield '-4/3 - Step 0.01' => [
            -4, // value
            3, // divide
            null, // decimal value
            null, // step
            [
                -1.33,
                -1.33,
                -1.33,
            ], // expected result
            -0.01, // remainder
        ];

        yield '1/1 - Step 0.01' => [
            1, // value
            1, // divide
            null, // decimal value
            null, // step
            [
                1,
            ], // expected result
            0, // remainder
        ];

        yield '5/2 - Step 0.01' => [
            5, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                2.5,
                2.5,
            ], // expected result
            0, // remainder
        ];

        yield '11.11/2 - Step 0.01' => [
            11.11, // value
            2, // divide
            null, // decimal value
            null, // step
            [
                5.55,
                5.55,
            ], // expected result
            0.01, // remainder
        ];

        yield '11.11/2 - Step 0.1' => [
            11.11, // value
            2, // divide
            null, // decimal value
            0.1, // step
            [
                5.5,
                5.5,
            ], // expected result
            0.11, // remainder
        ];

        yield '23,3332/3 - Step 0.001' => [
            23.3332, // value
            3, // divide
            3, // decimal value
            null, // step
            [
                7.777,
                7.777,
                7.777,
            ], // expected result
            0.0022, // remainder
        ];

        yield '100/3 - Step 0.05' => [
            100, // value
            3, // divide
            null, // decimal value
            0.05, // step
            [
                33.3,
                33.3,
                33.3,
            ], // expected result
            0.1, // remainder
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
            0, // remainder
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
            0.01, // remainder
        ];

        yield '100.31/3 - Step 0.1' => [
            100.31, // value
            3, // divide
            null, // decimal value
            0.1, // step
            [
                33.4,
                33.4,
                33.4,
            ], // expected result
            0.11, // remainder
        ];

        yield '2.9/2 - Step 0.03' => [
            2.9, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.44,
                1.44,
            ], // expected result
            0.02, // remainder
        ];

        yield '2.91/2 - Step 0.03' => [
            2.91, // value
            2, // divide
            null, // decimal value
            0.03, // step
            [
                1.44,
                1.44,
            ], // expected result
            0.03, // remainder
        ];

        yield '5099/7 - Step 0.11' => [
            5099, // value
            7, // divide
            null, // decimal value
            0.11, // step
            [
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
            ], // expected result
            0.06, // remainder
        ];

        yield '5099.17/7 - Step 0.11' => [
            5099.17, // value
            7, // divide
            null, // decimal value
            0.11, // step
            [
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
                728.42,
            ], // expected result
            0.23, // remainder
        ];

        yield '7.315/3 - Step 0.05' => [
            7.315, // value
            3, // divide
            3, // decimal value
            0.05, // step
            [
                2.4,
                2.4,
                2.4,
            ], // expected result
            0.115, // remainder
        ];

        yield '7.315/3 - Step 0.1' => [
            7.315, // value
            3, // divide
            1, // decimal value
            0.05, // step
            [
                2.4,
                2.4,
                2.4,
            ], // expected result
            0.115, // remainder
        ];
    }
}
