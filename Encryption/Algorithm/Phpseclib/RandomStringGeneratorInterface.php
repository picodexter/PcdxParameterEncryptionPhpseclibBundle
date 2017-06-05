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

/**
 * RandomStringGeneratorInterface.
 */
interface RandomStringGeneratorInterface
{
    /**
     * Generate random string.
     *
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString($length);
}
