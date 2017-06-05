<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher;

use phpseclib\Crypt\Base as BaseCipher;

/**
 * CipherFactoryInterface.
 */
interface CipherFactoryInterface
{
    /**
     * Create cipher.
     *
     * @return BaseCipher
     */
    public function createCipher();
}
