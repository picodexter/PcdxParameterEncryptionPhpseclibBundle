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
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Merge\InitializationVector\ValueMergerInterface;
use Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\InitializationVectorGeneratorInterface;

/**
 * PhpseclibInitializationVectorCipherEncrypterHandler.
 */
class PhpseclibInitializationVectorCipherEncrypterHandler implements EncrypterHandlerInterface
{
    /**
     * @var CipherGeneratorInterface
     */
    private $cipherGenerator;

    /**
     * @var InitializationVectorGeneratorInterface
     */
    private $ivGenerator;

    /**
     * @var int|null
     */
    private $keyLength = null;

    /**
     * @var ValueMergerInterface
     */
    private $valueMerger;

    /**
     * Constructor.
     *
     * @param CipherGeneratorInterface                     $cipherGenerator
     * @param InitializationVectorGeneratorInterface       $ivGenerator
     * @param ValueMergerInterface                         $valueMerger
     * @param int|null                                     $keyLength
     */
    public function __construct(
        CipherGeneratorInterface $cipherGenerator,
        InitializationVectorGeneratorInterface $ivGenerator,
        ValueMergerInterface $valueMerger,
        $keyLength = null
    ) {
        $this->cipherGenerator = $cipherGenerator;
        $this->ivGenerator = $ivGenerator;
        $this->valueMerger = $valueMerger;
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
    public function encryptValue($plainValue, $encryptionKey = null)
    {
        try {
            $cipher = $this->cipherGenerator->generateCipher($encryptionKey, $this->keyLength);

            $initializationVector = $this->ivGenerator->generateInitializationVector($cipher);

            $cipher->setIV($initializationVector);

            $encryptedValue = $cipher->encrypt($plainValue);

            $mergedValue = $this->valueMerger->merge($encryptedValue, $initializationVector);

            return $mergedValue;
        } catch (Exception $e) {
            throw new EncrypterException($e);
        }
    }
}
