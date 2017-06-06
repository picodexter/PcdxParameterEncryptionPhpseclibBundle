<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\Handler;

use Exception;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\EncoderInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;

/**
 * PhpseclibEncodedCipherEncrypterHandler.
 */
class PhpseclibEncodedCipherEncrypterHandler implements EncrypterHandlerInterface
{
    /**
     * @var CipherGeneratorInterface
     */
    private $cipherGenerator;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * Constructor.
     *
     * @param CipherGeneratorInterface $cipherGenerator
     * @param EncoderInterface         $encoder
     */
    public function __construct(CipherGeneratorInterface $cipherGenerator, EncoderInterface $encoder)
    {
        $this->cipherGenerator = $cipherGenerator;
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function encryptValue($plainValue, $encryptionKey = null)
    {
        try {
            $cipher = $this->cipherGenerator->generateCipher($encryptionKey);

            $encryptedValue = $cipher->encrypt($plainValue);

            $encodedValue = $this->encoder->encode($encryptedValue);

            return $encodedValue;
        } catch (Exception $e) {
            throw new EncrypterException($e);
        }
    }
}
