<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Decrypter;

use Exception;
use phpseclib\Crypt\RSA;
use Picodexter\ParameterEncryptionBundle\Encryption\Decrypter\DecrypterInterface;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Encoding\DecoderInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\PhpseclibRsaDecrypter;

class PhpseclibRsaDecrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RSA|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipher;

    /**
     * @var DecoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $decoder;

    /**
     * @var DecrypterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $decrypter;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipher = $this->createRsaMock();
        $this->decoder = $this->createDecoderInterfaceMock();

        $this->decrypter = new PhpseclibRsaDecrypter($this->cipher, $this->decoder);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->decrypter = null;
        $this->decoder = null;
        $this->cipher = null;
    }

    /**
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException
     */
    public function testDecryptValueException()
    {
        $encryptedValue = 'encrypted value';

        $this->cipher->expects($this->once())
            ->method('decrypt')
            ->will($this->throwException(new Exception()));

        $this->decrypter->decryptValue($encryptedValue);
    }

    public function testDecryptValueSuccessWithKey()
    {
        $encryptedValue = 'encoded encrypted value';
        $decryptionKey = 'some key';
        $prepDecodedValue = 'decoded encrypted value';
        $prepDecryptedValue = 'decrypted value';

        $this->setUpDecoderDecode($encryptedValue, $prepDecodedValue);

        $this->setUpCipherLoadKey($decryptionKey);

        $this->setUpCipherDecrypt($prepDecodedValue, $prepDecryptedValue);

        $decryptedValue = $this->decrypter->decryptValue($encryptedValue, $decryptionKey);

        $this->assertSame($prepDecryptedValue, $decryptedValue);
    }

    public function testDecryptValueSuccessWithoutKey()
    {
        $encryptedValue = 'encoded encrypted value';
        $prepDecodedValue = 'some key';
        $prepDecryptedValue = 'decrypted value';

        $this->setUpDecoderDecode($encryptedValue, $prepDecodedValue);

        $this->setUpCipherLoadKey(null);

        $this->setUpCipherDecrypt($prepDecodedValue, $prepDecryptedValue);

        $decryptedValue = $this->decrypter->decryptValue($encryptedValue);

        $this->assertSame($prepDecryptedValue, $decryptedValue);
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
     * Create mock for RSA.
     *
     * @return RSA|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRsaMock()
    {
        return $this->getMockBuilder(RSA::class)->getMock();
    }

    /**
     * Set up cipher: decrypt.
     *
     * @param string $decodedValue
     * @param string $decryptedValue
     */
    private function setUpCipherDecrypt($decodedValue, $decryptedValue)
    {
        $this->cipher->expects($this->once())
            ->method('decrypt')
            ->with($this->identicalTo($decodedValue))
            ->will($this->returnValue($decryptedValue));
    }

    /**
     * Set up cipher: loadKey.
     *
     * @param string|null $decryptionKey
     */
    private function setUpCipherLoadKey($decryptionKey)
    {
        $this->cipher->expects($this->once())
            ->method('loadKey')
            ->with($this->identicalTo($decryptionKey));
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
