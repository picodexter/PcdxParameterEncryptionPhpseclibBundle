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
use Picodexter\ParameterEncryptionBundle\Encryption\Value\Merge\InitializationVector\ValueMergerInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\Cipher\CipherGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\InitializationVectorGeneratorInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\Handler\PhpseclibInitializationVectorCipherEncrypterHandler;
use ReflectionProperty;

class PhpseclibInitializationVectorCipherEncrypterHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CipherGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cipherGenerator;

    /**
     * @var PhpseclibInitializationVectorCipherEncrypterHandler
     */
    private $handler;

    /**
     * @var InitializationVectorGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ivGenerator;

    /**
     * @var ValueMergerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $valueMerger;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->cipherGenerator = $this->createCipherGeneratorInterfaceMock();
        $this->ivGenerator = $this->createInitializationVectorGeneratorInterfaceMock();
        $this->valueMerger = $this->createValueMergerInterfaceMock();

        $this->handler = new PhpseclibInitializationVectorCipherEncrypterHandler(
            $this->cipherGenerator,
            $this->ivGenerator,
            $this->valueMerger
        );
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->handler = null;
        $this->valueMerger = null;
        $this->ivGenerator = null;
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
        $handler = new PhpseclibInitializationVectorCipherEncrypterHandler(
            $this->cipherGenerator,
            $this->ivGenerator,
            $this->valueMerger,
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
     * @expectedException \Picodexter\ParameterEncryptionBundle\Exception\Encryption\EncrypterException
     */
    public function testEncryptValueException()
    {
        $plainValue = 'plain value';

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
        $encryptionKey = 'some key';
        $preparedIv = 'some IV';
        $preparedEncrypted = 'encrypted value';
        $preparedMergedValue = 'merged IV + encrypted value';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher($encryptionKey, $cipher);

        $this->setUpInitializationVectorGeneratorGenerateInitializationVector($cipher, $preparedIv);

        $this->setUpBaseCipherSetIv($cipher, $preparedIv);

        $this->setUpBaseCipherEncrypt($cipher, $plainValue, $preparedEncrypted);

        $this->setUpValueMergerMerge($preparedEncrypted, $preparedIv, $preparedMergedValue);

        $mergedValue = $this->handler->encryptValue($plainValue, $encryptionKey);

        $this->assertSame($preparedMergedValue, $mergedValue);
    }

    public function testEncryptValueSuccessWithoutKey()
    {
        $plainValue = 'plain text';
        $preparedIv = 'some IV';
        $preparedEncrypted = 'encrypted value';
        $preparedMergedValue = 'merged IV + encrypted value';

        $cipher = $this->createBaseCipherMock();

        $this->setUpCipherGeneratorGenerateCipher(null, $cipher);

        $this->setUpInitializationVectorGeneratorGenerateInitializationVector($cipher, $preparedIv);

        $this->setUpBaseCipherSetIv($cipher, $preparedIv);

        $this->setUpBaseCipherEncrypt($cipher, $plainValue, $preparedEncrypted);

        $this->setUpValueMergerMerge($preparedEncrypted, $preparedIv, $preparedMergedValue);

        $mergedValue = $this->handler->encryptValue($plainValue);

        $this->assertSame($preparedMergedValue, $mergedValue);
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
     * Create mock for InitializationVectorGeneratorInterface.
     *
     * @return InitializationVectorGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createInitializationVectorGeneratorInterfaceMock()
    {
        return $this->getMockBuilder(InitializationVectorGeneratorInterface::class)->getMock();
    }

    /**
     * Create mock for ValueMergerInterface.
     *
     * @return ValueMergerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createValueMergerInterfaceMock()
    {
        return $this->getMockBuilder(ValueMergerInterface::class)->getMock();
    }

    /**
     * Get keyLength from PhpseclibInitializationVectorCipherEncrypterHandler.
     *
     * @param PhpseclibInitializationVectorCipherEncrypterHandler $handler
     *
     * @return mixed
     */
    private function getKeyLengthFromHandler(PhpseclibInitializationVectorCipherEncrypterHandler $handler)
    {
        $reflectionProperty = new ReflectionProperty(
            PhpseclibInitializationVectorCipherEncrypterHandler::class,
            'keyLength'
        );

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($handler);
    }

    /**
     * Set up BaseCipher: encrypt.
     *
     * @param BaseCipher|\PHPUnit_Framework_MockObject_MockObject $cipher
     * @param string                                              $plainValue
     * @param string                                              $encryptedValue
     */
    private function setUpBaseCipherEncrypt($cipher, $plainValue, $encryptedValue)
    {
        $cipher->expects($this->once())
            ->method('encrypt')
            ->with($this->identicalTo($plainValue))
            ->will($this->returnValue($encryptedValue));
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
     * @param string|null $encryptionKey
     * @param BaseCipher  $cipher
     */
    private function setUpCipherGeneratorGenerateCipher($encryptionKey, BaseCipher $cipher)
    {
        $this->cipherGenerator->expects($this->once())
            ->method('generateCipher')
            ->with(
                $this->identicalTo($encryptionKey),
                $this->identicalTo(null)
            )
            ->will($this->returnValue($cipher));
    }

    /**
     * Set up InitializationVectorGenerator: generateInitializationVector.
     *
     * @param BaseCipher $cipher
     * @param string     $initializationVector
     */
    private function setUpInitializationVectorGeneratorGenerateInitializationVector($cipher, $initializationVector)
    {
        $this->ivGenerator->expects($this->once())
            ->method('generateInitializationVector')
            ->with($this->identicalTo($cipher))
            ->will($this->returnValue($initializationVector));
    }

    /**
     * Set up ValueMerger: merge.
     *
     * @param string $encryptedValue
     * @param string $initializationVector
     * @param string $mergedValue
     */
    private function setUpValueMergerMerge($encryptedValue, $initializationVector, $mergedValue)
    {
        $this->valueMerger->expects($this->once())
            ->method('merge')
            ->with(
                $this->identicalTo($encryptedValue),
                $this->identicalTo($initializationVector)
            )
            ->will($this->returnValue($mergedValue));
    }
}
