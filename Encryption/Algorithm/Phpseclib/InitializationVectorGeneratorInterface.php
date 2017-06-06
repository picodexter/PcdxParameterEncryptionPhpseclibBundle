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

use phpseclib\Crypt\Base as BaseCipher;

/**
 * InitializationVectorGeneratorInterface.
 */
interface InitializationVectorGeneratorInterface
{
    /**
     * Generate initialization vector.
     *
     * @param BaseCipher $cipher
     *
     * @return string
     */
    public function generateInitializationVector(BaseCipher $cipher);
}
