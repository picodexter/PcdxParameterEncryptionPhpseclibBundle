<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Algorithm\Phpseclib\Cipher;

use phpseclib\Crypt\DES;
use phpseclib\Crypt\Rijndael;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherFactory;
use ReflectionProperty;

class CipherFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSuccess()
    {
        $cipherTemplate = new Rijndael();

        $factory = new CipherFactory($cipherTemplate);

        $className = $this->getClassNameFromFactory($factory);

        $this->assertSame(get_class($cipherTemplate), $className);
    }

    public function testCreateCipherSuccess()
    {
        $cipherTemplate = new DES();

        $factory = new CipherFactory($cipherTemplate);

        $cipher = $factory->createCipher();

        $this->assertInstanceOf(DES::class, $cipher);
        $this->assertNotSame($cipherTemplate, $cipher);
    }

    /**
     * Get class name from factory.
     *
     * @param CipherFactory $factory
     *
     * @return string
     */
    private function getClassNameFromFactory(CipherFactory $factory)
    {
        $reflectionProperty = new ReflectionProperty(CipherFactory::class, 'fullCipherClassName');

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($factory);
    }
}
