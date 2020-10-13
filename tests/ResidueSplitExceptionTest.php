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
use Romainnorberg\Residue\Residue;

class ResidueSplitExceptionTest extends TestCase
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
}
