<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Algorithm\Phpseclib;

use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\RandomStringGenerator;

class RandomStringGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateRandomStringSuccess()
    {
        $generator = new RandomStringGenerator(RandomDummy::class);

        $length = 246;

        $generatedString = $generator->generateRandomString($length);

        $this->assertSame('random string', $generatedString);
        $this->assertSame(
            'string',
            RandomDummy::$lastMethodCalled
        );
        $this->assertSame(
            [$length],
            RandomDummy::$lastCallArguments
        );
    }
}
