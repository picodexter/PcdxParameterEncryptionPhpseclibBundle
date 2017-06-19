<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter;

use Picodexter\ParameterEncryptionBundle\Encryption\Encrypter\EncrypterInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\Handler\EncrypterHandlerInterface;

/**
 * PhpseclibHandlerProxyEncrypter.
 */
class PhpseclibHandlerProxyEncrypter implements EncrypterInterface
{
    /**
     * @var EncrypterHandlerInterface
     */
    private $handler;

    /**
     * Constructor.
     *
     * @param EncrypterHandlerInterface $handler
     */
    public function __construct(EncrypterHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function encryptValue($plainValue, $encryptionKey)
    {
        return $this->handler->encryptValue($plainValue, $encryptionKey);
    }
}
