<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter;

use Picodexter\ParameterEncryptionBundle\Encryption\Decrypter\DecrypterInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\Handler\DecrypterHandlerInterface;

/**
 * PhpseclibHandlerProxyDecrypter.
 */
class PhpseclibHandlerProxyDecrypter implements DecrypterInterface
{
    /**
     * @var DecrypterHandlerInterface
     */
    private $handler;

    /**
     * Constructor.
     *
     * @param DecrypterHandlerInterface $handler
     */
    public function __construct(DecrypterHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function decryptValue($encryptedValue, $decryptionKey = null)
    {
        return $this->handler->decryptValue($encryptedValue, $decryptionKey);
    }
}
