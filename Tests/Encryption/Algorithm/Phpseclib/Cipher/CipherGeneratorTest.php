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

use phpseclib\Crypt\Base as BaseCipher;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherFactoryInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGenerator;

class CipherGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CipherFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var CipherGenerator
     */
    private $generator;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->factory = $this->createCipherFactoryInterfaceMock();

        $this->generator = new CipherGenerator($this->factory);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->generator = null;
        $this->factory = null;
    }

    public function testGenerateCipherSuccessWithKeyLength()
    {
        $key = 'some_key';
        $keyLength = 123;

        $preparedCipher = $this->createBaseCipherMock();

        $this->setUpFactoryCreateCipher($preparedCipher);

        $preparedCipher->expects($this->once())
            ->method('setKeyLength')
            ->with($this->identicalTo($keyLength));

        $this->setUpBaseCipherSetKey($preparedCipher, $key);

        $cipher = $this->generator->generateCipher($key, $keyLength);

        $this->assertInstanceOf(BaseCipher::class, $cipher);
        $this->assertSame($preparedCipher, $cipher);
    }

    public function testGenerateCipherSuccessWithoutKeyLength()
    {
        $key = 'some_key';

        $preparedCipher = $this->createBaseCipherMock();

        $this->setUpFactoryCreateCipher($preparedCipher);

        $preparedCipher->expects($this->never())
            ->method('setKeyLength')
            ->with($this->identicalTo(null));

        $this->setUpBaseCipherSetKey($preparedCipher, $key);

        $cipher = $this->generator->generateCipher($key);

        $this->assertInstanceOf(BaseCipher::class, $cipher);
        $this->assertSame($preparedCipher, $cipher);
    }

    /**
     * Create mock for BaseCipher.
     *
     * @return BaseCipher|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createBaseCipherMock()
    {
        return $this->getMockBuilder(BaseCipher::class)->getMock();
    }

    /**
     * Create mock for CipherFactoryInterface.
     *
     * @return CipherFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCipherFactoryInterfaceMock()
    {
        return $this->getMockBuilder(CipherFactoryInterface::class)->getMock();
    }

    /**
     * Set up Factory: createCipher.
     *
     * @param BaseCipher $cipher
     */
    private function setUpFactoryCreateCipher(BaseCipher $cipher)
    {
        $this->factory->expects($this->once())
            ->method('createCipher')
            ->with()
            ->will($this->returnValue($cipher));
    }

    /**
     * Set up BaseCipher: setKey.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $key
     */
    private function setUpBaseCipherSetKey(BaseCipher $cipher, $key)
    {
        $cipher->expects($this->once())
            ->method('setKey')
            ->with($this->identicalTo($key));
    }
}
