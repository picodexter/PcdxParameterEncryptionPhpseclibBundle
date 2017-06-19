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

use Exception;
use phpseclib\Crypt\RSA;
use Picodexter\ParameterEncryptionBundle\Encryption\Decrypter\DecrypterInterface;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\DecoderInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException;

/**
 * PhpseclibRsaDecrypter.
 */
class PhpseclibRsaDecrypter implements DecrypterInterface
{
    /**
     * @var RSA
     */
    private $cipher;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * Constructor.
     *
     * @param RSA              $cipher
     * @param DecoderInterface $decoder
     */
    public function __construct(RSA $cipher, DecoderInterface $decoder)
    {
        $this->cipher = $cipher;
        $this->decoder = $decoder;
    }

    /**
     * @inheritDoc
     */
    public function decryptValue($encryptedValue, $decryptionKey)
    {
        try {
            $decodedValue = $this->decoder->decode($encryptedValue);

            $this->cipher->loadKey($decryptionKey);

            $decryptedValue = $this->cipher->decrypt($decodedValue);

            return $decryptedValue;
        } catch (Exception $e) {
            throw new DecrypterException($e);
        }
    }
}
