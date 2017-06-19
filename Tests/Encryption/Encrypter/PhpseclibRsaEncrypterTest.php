<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Encrypter;

use Exception;
use phpseclib\Crypt\RSA;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\EncoderInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\PhpseclibRsaEncrypter;

class PhpseclibRsaEncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RSA|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipher;

    /**
     * @var EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encoder;

    /**
     * @var PhpseclibRsaEncrypter
     */
    private $encrypter;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipher = $this->createRsaMock();
        $this->encoder = $this->createEncoderInterfaceMock();

        $this->encrypter = new PhpseclibRsaEncrypter($this->cipher, $this->encoder);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->encrypter = null;
        $this->encoder = null;
        $this->cipher = null;
    }

    /**
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException
     */
    public function testEncryptValueException()
    {
        $plainValue = 'plain value';
        $encryptionKey = 'some key';

        $this->cipher->expects($this->once())
            ->method('encrypt')
            ->will($this->throwException(new Exception()));

        $this->encrypter->encryptValue($plainValue, $encryptionKey);
    }

    public function testEncryptValueSuccess()
    {
        $plainValue = 'plain value';
        $encryptionKey = 'some key';
        $prepEncryptedValue = 'encrypted value';
        $prepEncodedValue = 'encoded encrypted value';

        $this->cipher->expects($this->once())
            ->method('loadKey')
            ->with($this->identicalTo($encryptionKey));

        $this->cipher->expects($this->once())
            ->method('encrypt')
            ->with($this->identicalTo($plainValue))
            ->will($this->returnValue($prepEncryptedValue));

        $this->encoder->expects($this->once())
            ->method('encode')
            ->with($this->identicalTo($prepEncryptedValue))
            ->will($this->returnValue($prepEncodedValue));

        $encryptedValue = $this->encrypter->encryptValue($plainValue, $encryptionKey);

        $this->assertSame($prepEncodedValue, $encryptedValue);
    }

    /**
     * Create mock for EncoderInterface.
     *
     * @return EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createEncoderInterfaceMock()
    {
        return $this->getMockBuilder(EncoderInterface::class)->getMock();
    }

    /**
     * Create mock for RSA.
     *
     * @return RSA|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRsaMock()
    {
        return $this->getMockBuilder(RSA::class)->getMock();
    }
}
