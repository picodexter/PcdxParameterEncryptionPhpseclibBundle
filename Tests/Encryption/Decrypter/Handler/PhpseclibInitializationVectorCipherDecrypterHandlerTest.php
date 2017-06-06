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
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Merge\InitializationVector\SplitValueBag;
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Merge\InitializationVector\ValueSplitterInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\Handler\PhpseclibInitializationVectorCipherDecrypterHandler;
use ReflectionProperty;

class PhpseclibInitializationVectorCipherDecrypterHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CipherGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipherGenerator;

    /**
     * @var PhpseclibInitializationVectorCipherDecrypterHandler
     */
    private $handler;

    /**
     * @var ValueSplitterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $valueSplitter;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipherGenerator = $this->createCipherGeneratorInterfaceMock();
        $this->valueSplitter = $this->createValueSplitterInterfaceMock();

        $this->handler = new PhpseclibInitializationVectorCipherDecrypterHandler(
            $this->cipherGenerator,
            $this->valueSplitter
        );
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->handler = null;
        $this->valueSplitter = null;
        $this->cipherGenerator = null;
    }

    /**
     * @param mixed    $keyLength
     * @param int|null $expectedKeyLength
     *
     * @dataProvider provideKeyLengthData
     */
    public function testConstructorSuccess($keyLength, $expectedKeyLength)
    {
        $handler = new PhpseclibInitializationVectorCipherDecrypterHandler(
            $this->cipherGenerator,
            $this->valueSplitter,
            $keyLength
        );

        $savedKeyLength = $this->getKeyLengthFromHandler($handler);

        $this->assertSame($expectedKeyLength, $savedKeyLength);
    }

    /**
     * Data provider.
     */
    public function provideKeyLengthData()
    {
        return [
            'null' => [
                null,
                null,
            ],
            'false' => [
                false,
                0,
            ],
            'true' => [
                true,
                1,
            ],
            'integer' => [
                3,
                3,
            ],
            'float' => [
                12.34,
                12,
            ],
        ];
    }

    /**
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\DecrypterException
     */
    public function testDecryptValueException()
    {
        $encryptedValue = 'encrypted value';
        $blockLength = 128;

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $this->setUpBaseCipherGetBlockLength($cipher, $blockLength);

        $this->valueSplitter->expects($this->once())
            ->method('split')
            ->with(
                $this->identicalTo($encryptedValue),
                $this->identicalTo((int) floor($blockLength / 8))
            )
            ->will($this->throwException(new Exception()));

        $this->handler->decryptValue($encryptedValue);
    }

    public function testDecryptValueSuccessWithKey()
    {
        $mergedValue = 'merged encrypted value';
        $decryptionKey = 'some key';
        $blockLength = 128;
        $preparedIv = 'some IV';
        $preparedEncrypted = 'encrypted value';
        $preparedDecrypted = 'decrypted value';

        $cipher = $this->createBaseCipherMock();
        $decodedValueBag = $this->createSplitValueBagMock();

        $this->setUpCipherGeneratorGenerateCipher($decryptionKey, $cipher);

        $this->setUpBaseCipherGetBlockLength($cipher, $blockLength);

        $this->setUpValueSplitterSplit($mergedValue, $blockLength, $decodedValueBag);

        $this->setUpSplitValueBagGetInitializationVector($decodedValueBag, $preparedIv);

        $this->setUpBaseCipherSetIv($cipher, $preparedIv);

        $this->setUpSplitValueBagGetEncryptedValue($decodedValueBag, $preparedEncrypted);

        $this->setUpBaseCipherDecrypt($cipher, $preparedEncrypted, $preparedDecrypted);

        $decryptedValue = $this->handler->decryptValue($mergedValue, $decryptionKey);

        $this->assertSame($preparedDecrypted, $decryptedValue);
    }

    public function testDecryptValueSuccessWithoutKey()
    {
        $mergedValue = 'merged encrypted value';
        $blockLength = 128;
        $preparedIv = 'some IV';
        $preparedEncrypted = 'encrypted value';
        $preparedDecrypted = 'decrypted value';

        $cipher = $this->createBaseCipherMock();
        $decodedValueBag = $this->createSplitValueBagMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $this->setUpBaseCipherGetBlockLength($cipher, $blockLength);

        $this->setUpValueSplitterSplit($mergedValue, $blockLength, $decodedValueBag);

        $this->setUpSplitValueBagGetInitializationVector($decodedValueBag, $preparedIv);

        $this->setUpBaseCipherSetIv($cipher, $preparedIv);

        $this->setUpSplitValueBagGetEncryptedValue($decodedValueBag, $preparedEncrypted);

        $this->setUpBaseCipherDecrypt($cipher, $preparedEncrypted, $preparedDecrypted);

        $decryptedValue = $this->handler->decryptValue($mergedValue);

        $this->assertSame($preparedDecrypted, $decryptedValue);
    }

    /**
     * @param mixed    $keyLength
     * @param int|null $expectedKeyLength
     *
     * @dataProvider provideKeyLengthData
     */
    public function testSetKeyLengthSuccess($keyLength, $expectedKeyLength)
    {
        $this->handler->setKeyLength($keyLength);

        $savedKeyLength = $this->getKeyLengthFromHandler($this->handler);

        $this->assertSame($expectedKeyLength, $savedKeyLength);
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
     * Create mock for SplitValueBag.
     *
     * @return SplitValueBag|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSplitValueBagMock()
    {
        return $this->getMockBuilder(SplitValueBag::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Create mock for ValueSplitterInterface.
     *
     * @return ValueSplitterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createValueSplitterInterfaceMock()
    {
        return $this->getMockBuilder(ValueSplitterInterface::class)->getMock();
    }

    /**
     * Get keyLength from PhpseclibInitializationVectorCipherDecrypterHandler.
     *
     * @param PhpseclibInitializationVectorCipherDecrypterHandler $handler
     *
     * @return mixed
     */
    private function getKeyLengthFromHandler(PhpseclibInitializationVectorCipherDecrypterHandler $handler)
    {
        $reflectionProperty = new ReflectionProperty(
            PhpseclibInitializationVectorCipherDecrypterHandler::class,
            'keyLength'
        );

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($handler);
    }

    /**
     * Set up BaseCipher: decrypt.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $encryptedValue
     * @param string                                              $decryptedValue
     */
    private function setUpBaseCipherDecrypt($cipher, $encryptedValue, $decryptedValue)
    {
        $cipher->expects($this->once())
            ->method('decrypt')
            ->with($this->identicalTo($encryptedValue))
            ->will($this->returnValue($decryptedValue));
    }

    /**
     * Set up BaseCipher: getBlockLength.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param int                                                 $blockLength
     */
    private function setUpBaseCipherGetBlockLength(BaseCipher $cipher, $blockLength)
    {
        $cipher->expects($this->once())
            ->method('getBlockLength')
            ->with()
            ->will($this->returnValue($blockLength));
    }

    /**
     * Set up BaseCipher: setIV.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $initializationVector
     */
    private function setUpBaseCipherSetIv(BaseCipher $cipher, $initializationVector)
    {
        $cipher->expects($this->once())
            ->method('setIV')
            ->with($this->identicalTo($initializationVector));
    }

    /**
     * Set up CipherGenerator: generateCipher.
     *
     * @param string|null $decryptionKey
     * @param BaseCipher  $cipher
     */
    private function setUpCipherGeneratorGenerateCipher($decryptionKey, BaseCipher $cipher)
    {
        $this->cipherGenerator->expects($this->once())
            ->method('generateCipher')
            ->with(
                $this->identicalTo($decryptionKey),
                $this->identicalTo(null)
            )
            ->will($this->returnValue($cipher));
    }

    /**
     * Set up SplitValueBag: getEncryptedValue.
     *
     * @param SplitValueBag|\PHPUnit_Framework_MockObject_MockObject $decodedValueBag
     * @param string                                                 $encryptedValue
     */
    private function setUpSplitValueBagGetEncryptedValue($decodedValueBag, $encryptedValue)
    {
        $decodedValueBag->expects($this->once())
            ->method('getEncryptedValue')
            ->with()
            ->will($this->returnValue($encryptedValue));
    }

    /**
     * Set up SplitValueBag: getInitializationVector.
     *
     * @param SplitValueBag|\PHPUnit_Framework_MockObject_MockObject $decodedValueBag
     * @param string                                                 $initializationVector
     */
    private function setUpSplitValueBagGetInitializationVector(SplitValueBag $decodedValueBag, $initializationVector)
    {
        $decodedValueBag->expects($this->once())
            ->method('getInitializationVector')
            ->with()
            ->will($this->returnValue($initializationVector));
    }

    /**
     * Set up ValueSplitter: split.
     *
     * @param string        $mergedValue
     * @param int           $blockLength
     * @param SplitValueBag $decodedValueBag
     */
    private function setUpValueSplitterSplit($mergedValue, $blockLength, $decodedValueBag)
    {
        $this->valueSplitter->expects($this->once())
            ->method('split')
            ->with(
                $this->identicalTo($mergedValue),
                $this->identicalTo((int) floor($blockLength / 8))
            )
            ->will($this->returnValue($decodedValueBag));
    }
}
