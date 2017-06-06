<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib;

use phpseclib\Crypt\Base as BaseCipher;

/**
 * InitializationVectorGenerator.
 */
class InitializationVectorGenerator implements InitializationVectorGeneratorInterface
{
    /**
     * @var RandomStringGeneratorInterface
     */
    private $randomStringGenerator;

    /**
     * Constructor.
     *
     * @param RandomStringGeneratorInterface $stringGenerator
     */
    public function __construct(RandomStringGeneratorInterface $stringGenerator)
    {
        $this->randomStringGenerator = $stringGenerator;
    }

    /**
     * @inheritDoc
     */
    public function generateInitializationVector(BaseCipher $cipher)
    {
        return $this->randomStringGenerator->generateRandomString($cipher->getBlockLength() >> 3);
    }
}
