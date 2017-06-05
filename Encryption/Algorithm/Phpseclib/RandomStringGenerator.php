<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib;

use phpseclib\Crypt\Random;

/**
 * RandomStringGenerator.
 */
class RandomStringGenerator implements RandomStringGeneratorInterface
{
    /**
     * @var string
     */
    private $fullyQualifiedClassName;

    /**
     * Constructor.
     *
     * @param string $completeClassName
     */
    public function __construct($completeClassName = Random::class)
    {
        $this->fullyQualifiedClassName = $completeClassName;
    }

    /**
     * @inheritDoc
     */
    public function generateRandomString($length)
    {
        return forward_static_call_array(
            [$this->fullyQualifiedClassName, 'string'],
            [$length]
        );
    }
}
