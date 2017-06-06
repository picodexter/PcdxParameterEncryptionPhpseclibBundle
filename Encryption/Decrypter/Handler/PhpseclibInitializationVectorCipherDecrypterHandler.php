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
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Merge\InitializationVector\ValueSplitterInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;

/**
 * PhpseclibInitializationVectorCipherDecrypterHandler.
 */
class PhpseclibInitializationVectorCipherDecrypterHandler implements DecrypterHandlerInterface
{
    /**
     * @var CipherGeneratorInterface
     */
    private $cipherGenerator;

    /**
     * @var int|null
     */
    private $keyLength = null;

    /**
     * @var ValueSplitterInterface
     */
    private $valueSplitter;

    /**
     * Constructor.
     *
     * @param CipherGeneratorInterface $cipherGenerator
     * @param ValueSplitterInterface   $valueSplitter
     * @param int|null                 $keyLength
     */
    public function __construct(
        CipherGeneratorInterface $cipherGenerator,
        ValueSplitterInterface $valueSplitter,
        $keyLength = null
    ) {
        $this->cipherGenerator = $cipherGenerator;
        $this->valueSplitter = $valueSplitter;
        $this->setKeyLength($keyLength);
    }

    /**
     * Setter: keyLength.
     *
     * @param int|null $keyLength
     */
    public function setKeyLength($keyLength)
    {
        $this->keyLength = (null === $keyLength ? null : (int) $keyLength);
    }

    /**
     * @inheritDoc
     */
    public function decryptValue($encryptedValue, $decryptionKey = null)
    {
        try {
            $cipher = $this->cipherGenerator->generateCipher($decryptionKey, $this->keyLength);

            $ivLength = ($cipher->getBlockLength() >> 3);

            $decodedValueBag = $this->valueSplitter->split($encryptedValue, $ivLength);

            $cipher->setIV($decodedValueBag->getInitializationVector());

            $decryptedValue = $cipher->decrypt($decodedValueBag->getEncryptedValue());

            return $decryptedValue;
        } catch (Exception $e) {
            throw new DecrypterException($e);
        }
    }
}
