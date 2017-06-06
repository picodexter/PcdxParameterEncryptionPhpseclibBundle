<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\Handler;

use Exception;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\DecoderInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;

/**
 * PhpseclibEncodedCipherDecrypterHandler.
 */
class PhpseclibEncodedCipherDecrypterHandler implements DecrypterHandlerInterface
{
    /**
     * @var CipherGeneratorInterface
     */
    private $cipherGenerator;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * Constructor.
     *
     * @param CipherGeneratorInterface $cipherGenerator
     * @param DecoderInterface         $decoder
     */
    public function __construct(CipherGeneratorInterface $cipherGenerator, DecoderInterface $decoder)
    {
        $this->cipherGenerator = $cipherGenerator;
        $this->decoder = $decoder;
    }

    /**
     * @inheritDoc
     */
    public function decryptValue($encryptedValue, $decryptionKey = null)
    {
        try {
            $cipher = $this->cipherGenerator->generateCipher($decryptionKey);

            $decodedValue = $this->decoder->decode($encryptedValue);

            $decryptedValue = $cipher->decrypt($decodedValue);

            return $decryptedValue;
        } catch (Exception $e) {
            throw new DecrypterException($e);
        }
    }
}
