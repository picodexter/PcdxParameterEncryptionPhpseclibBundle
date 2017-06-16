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

use Exception;
use phpseclib\Crypt\RSA;
use Picodexter\ParameterEncryptionBundle\Encryption\Encrypter\EncrypterInterface;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\EncoderInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException;

/**
 * PhpseclibRsaEncrypter.
 */
class PhpseclibRsaEncrypter implements EncrypterInterface
{
    /**
     * @var RSA
     */
    private $cipher;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * Constructor.
     *
     * @param RSA              $cipher
     * @param EncoderInterface $encoder
     */
    public function __construct(RSA $cipher, EncoderInterface $encoder)
    {
        $this->cipher = $cipher;
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function encryptValue($plainValue, $encryptionKey = null)
    {
        try {
            $this->cipher->loadKey($encryptionKey);

            $encryptedValue = $this->cipher->encrypt($plainValue);

            $encodedValue = $this->encoder->encode($encryptedValue);

            return $encodedValue;
        } catch (Exception $e) {
            throw new EncrypterException($e);
        }
    }
}
