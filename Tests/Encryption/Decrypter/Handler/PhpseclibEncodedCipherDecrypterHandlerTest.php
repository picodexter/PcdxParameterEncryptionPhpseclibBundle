<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Decrypter\Handler;

use Exception;
use phpseclib\Crypt\Base as BaseCipher;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\DecoderInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\Handler\PhpseclibEncodedCipherDecrypterHandler;

class PhpseclibEncodedCipherDecrypterHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CipherGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipherGenerator;

    /**
     * @var DecoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $decoder;

    /**
     * @var PhpseclibEncodedCipherDecrypterHandler
     */
    private $handler;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipherGenerator = $this->createCipherGeneratorInterfaceMock();
        $this->decoder = $this->createDecoderInterfaceMock();

        $this->handler = new PhpseclibEncodedCipherDecrypterHandler($this->cipherGenerator, $this->decoder);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->handler = null;
        $this->decoder = null;
        $this->cipherGenerator = null;
    }

    /**
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException
     */
    public function testDecryptValueException()
    {
        $encryptedValue = 'encrypted value';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $cipher->expects($this->once())
            ->method('decrypt')
            ->will($this->throwException(new Exception()));

        $this->handler->decryptValue($encryptedValue);
    }

    public function testDecryptValueSuccessWithKey()
    {
        $encryptedValue = 'encrypted value';
        $decryptionKey = 'some key';
        $preparedDecodedValue = 'decoded encrypted value';
        $preparedDecrypted = 'decrypted value';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher($decryptionKey, $cipher);

        $this->setUpDecoderDecode($encryptedValue, $preparedDecodedValue);

        $this->setUpBaseCipherDecrypt($cipher, $preparedDecodedValue, $preparedDecrypted);

        $decryptedValue = $this->handler->decryptValue($encryptedValue, $decryptionKey);

        $this->assertSame($preparedDecrypted, $decryptedValue);
    }

    public function testDecryptValueSuccessWithoutKey()
    {
        $encryptedValue = 'encrypted value';
        $preparedDecodedValue = 'decoded encrypted value';
        $preparedDecrypted = 'decrypted value';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $this->setUpDecoderDecode($encryptedValue, $preparedDecodedValue);

        $this->setUpBaseCipherDecrypt($cipher, $preparedDecodedValue, $preparedDecrypted);

        $decryptedValue = $this->handler->decryptValue($encryptedValue);

        $this->assertSame($preparedDecrypted, $decryptedValue);
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
     * Create mock for DecoderInterface.
     *
     * @return DecoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createDecoderInterfaceMock()
    {
        return $this->getMockBuilder(DecoderInterface::class)->getMock();
    }

    /**
     * Set up Cipher: decrypt.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $decodedValue
     * @param string                                              $decryptedValue
     */
    private function setUpBaseCipherDecrypt(BaseCipher $cipher, $decodedValue, $decryptedValue)
    {
        $cipher->expects($this->once())
            ->method('decrypt')
            ->with($this->identicalTo($decodedValue))
            ->will($this->returnValue($decryptedValue));
    }

    /**
     * Set up CipherGenerator: generateCipher.
     *
     * @param string     $decryptionKey
     * @param BaseCipher $cipher
     */
    private function setUpCipherGeneratorGenerateCipher($decryptionKey, BaseCipher $cipher)
    {
        $this->cipherGenerator->expects($this->once())
            ->method('generateCipher')
            ->with($this->identicalTo($decryptionKey))
            ->will($this->returnValue($cipher));
    }

    /**
     * Set up Decoder: decode.
     *
     * @param string $encryptedValue
     * @param string $decodedValue
     */
    private function setUpDecoderDecode($encryptedValue, $decodedValue)
    {
        $this->decoder->expects($this->once())
            ->method('decode')
            ->with($this->identicalTo($encryptedValue))
            ->will($this->returnValue($decodedValue));
    }
}
