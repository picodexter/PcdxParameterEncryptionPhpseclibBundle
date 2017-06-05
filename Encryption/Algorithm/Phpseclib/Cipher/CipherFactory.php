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
use ReflectionClass;

/**
 * CipherFactory.
 */
class CipherFactory implements CipherFactoryInterface
{
    /**
     * @var string
     */
    private $fullCipherClassName;

    /**
     * Constructor.
     *
     * @param BaseCipher $cipherTemplate
     */
    public function __construct(BaseCipher $cipherTemplate)
    {
        $this->fullCipherClassName = get_class($cipherTemplate);
    }

    /**
     * @inheritDoc
     */
    public function createCipher()
    {
        $reflectionClass = new ReflectionClass($this->fullCipherClassName);

        return $reflectionClass->newInstance();
    }
}
