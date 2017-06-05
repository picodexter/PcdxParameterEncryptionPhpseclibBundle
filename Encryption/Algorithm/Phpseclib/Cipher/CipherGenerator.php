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

/**
 * CipherGenerator.
 */
class CipherGenerator implements CipherGeneratorInterface
{
    /**
     * @var CipherFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param CipherFactoryInterface $factory
     */
    public function __construct(CipherFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function generateCipher($key, $keyLength = null)
    {
        $cipher = $this->factory->createCipher();

        if (null !== $keyLength) {
            $cipher->setKeyLength($keyLength);
        }

        $cipher->setKey($key);

        return $cipher;
    }
}
