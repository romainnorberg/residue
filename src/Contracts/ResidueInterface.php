<?php

/*
 * This file is part of the Residue package.
 * (c) Romain Norberg <romainnorberg@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romainnorberg\Residue\Contracts;

use Generator;

interface ResidueInterface
{
    public const DEFAULT_DECIMAL = 2;

    public const SPLIT_MODE_ALLOCATE = 'allocate';
    public const SPLIT_MODE_EQUITY = 'equity';
    public const SPLIT_MODES = [
        self::SPLIT_MODE_ALLOCATE,
        self::SPLIT_MODE_EQUITY,
    ];

    public function divideBy(int $divider): self;

    public function step(float $step): self;

    public function decimal(int $decimal): self;

    /**
     * @param string $mode one of self::SPLIT_MODES
     *
     * SPLIT_MODE_ALLOCATE is default and try to allocate the maximum of the value according to step
     * example:
     *   100/3 with step 1 will split into [34, 33, 33]
     *   or
     *   101/3 with step 1 will split into [34, 34, 33]
     * while
     * SPLIT_MODE_EQUITY try to allocate equally the maximum of the value according to step
     * example:
     *   100/3 with step 1 will split into [33, 33, 33]
     *   or
     *   101/3 with step 1 will split again into [33, 33, 33]
     */
    public function split(string $mode = self::SPLIT_MODE_ALLOCATE): Generator;

    public function toArray(string $mode = self::SPLIT_MODE_ALLOCATE): array;

    public function getRemainder(): float;
}
