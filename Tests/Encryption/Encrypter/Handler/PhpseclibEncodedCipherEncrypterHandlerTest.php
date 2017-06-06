<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Encrypter\Handler;

use Exception;
use phpseclib\Crypt\Base as BaseCipher;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\EncoderInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\Handler\PhpseclibEncodedCipherEncrypterHandler;

class PhpseclibEncodedCipherEncrypterHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CipherGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipherGenerator;

    /**
     * @var EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encoder;

    /**
     * @var PhpseclibEncodedCipherEncrypterHandler
     */
    private $handler;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipherGenerator = $this->createCipherGeneratorInterfaceMock();
        $this->encoder = $this->createEncoderInterfaceMock();

        $this->handler = new PhpseclibEncodedCipherEncrypterHandler($this->cipherGenerator, $this->encoder);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->handler = null;
        $this->encoder = null;
        $this->cipherGenerator = null;
    }

    /**
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException
     */
    public function testEncryptValueException()
    {
        $plainValue = 'plain text';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $cipher->expects($this->once())
            ->method('encrypt')
            ->with($this->identicalTo($plainValue))
            ->will($this->throwException(new Exception()));

        $this->handler->encryptValue($plainValue);
    }

    public function testEncryptValueSuccessWithKey()
    {
        $plainValue = 'plain text';
        $encryptionKey = 'encryption key';
        $preparedEncrypted = 'encrypted text';
        $preparedEncodedValue = 'encoded encrypted text';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher($encryptionKey, $cipher);

        $this->setUpBaseCipherEncrypt($cipher, $plainValue, $preparedEncrypted);

        $this->setUpEncoderEncode($preparedEncrypted, $preparedEncodedValue);

        $encodedValue = $this->handler->encryptValue($plainValue, $encryptionKey);

        $this->assertSame($preparedEncodedValue, $encodedValue);
    }

    public function testEncryptValueSuccessWithoutKey()
    {
        $plainValue = 'plain text';
        $preparedEncrypted = 'encrypted text';
        $preparedEncodedValue = 'encoded encrypted text';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $this->setUpBaseCipherEncrypt($cipher, $plainValue, $preparedEncrypted);

        $this->setUpEncoderEncode($preparedEncrypted, $preparedEncodedValue);

        $encodedValue = $this->handler->encryptValue($plainValue);

        $this->assertSame($preparedEncodedValue, $encodedValue);
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
     * Create mock for CipherGeneratorInterface.
     *
     * @return CipherGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCipherGeneratorInterfaceMock()
    {
        return $this->getMockBuilder(CipherGeneratorInterface::class)->getMock();
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
     * Set up BaseCipher: encrypt.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $plainValue
     * @param string                                              $preparedEncrypted
     */
    private function setUpBaseCipherEncrypt(BaseCipher $cipher, $plainValue, $preparedEncrypted)
    {
        $cipher->expects($this->once())
            ->method('encrypt')
            ->with($this->identicalTo($plainValue))
            ->will($this->returnValue($preparedEncrypted));
    }

    /**
     * Set up CipherGenerator: generateCipher.
     *
     * @param string|null $encryptionKey
     * @param BaseCipher  $cipher
     */
    private function setUpCipherGeneratorGenerateCipher($encryptionKey, BaseCipher $cipher)
    {
        $this->cipherGenerator->expects($this->once())
            ->method('generateCipher')
            ->with($this->identicalTo($encryptionKey))
            ->will($this->returnValue($cipher));
    }

    /**
     * Set up Encoder: encode.
     *
     * @param string $encryptedValue
     * @param string $preparedEncodedValue
     */
    private function setUpEncoderEncode($encryptedValue, $preparedEncodedValue)
    {
        $this->encoder->expects($this->once())
            ->method('encode')
            ->with($this->identicalTo($encryptedValue))
            ->will($this->returnValue($preparedEncodedValue));
    }
}
