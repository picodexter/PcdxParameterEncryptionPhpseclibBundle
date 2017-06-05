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

/**
 * RandomDummy.
 */
class RandomDummy implements RandomDummyInterface
{
    /**
     * @var string
     */
    public static $lastMethodCalled = '';

    /**
     * @var array
     */
    public static $lastCallArguments = [];

    /**
     * @inheritDoc
     */
    static function string($length)
    {
        self::$lastMethodCalled = 'string';
        self::$lastCallArguments = func_get_args();

        return 'random string';
    }
}
