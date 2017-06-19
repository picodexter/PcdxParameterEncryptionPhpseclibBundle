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

use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\Handler\DecrypterHandlerInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Decrypter\PhpseclibHandlerProxyDecrypter;

class PhpseclibHandlerProxyDecrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhpseclibHandlerProxyDecrypter
     */
    private $decrypter;

    /**
     * @var DecrypterHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->handler = $this->createDecrypterHandlerInterfaceMock();

        $this->decrypter = new PhpseclibHandlerProxyDecrypter($this->handler);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->decrypter = null;
        $this->handler = null;
    }

    public function testDecryptValueSuccess()
    {
        $encryptedValue = 'encrypted value';
        $decryptionKey = 'some key';
        $preparedDecrypted = 'decrypted value';

        $this->handler->expects($this->once())
            ->method('decryptValue')
            ->with(
                $this->identicalTo($encryptedValue),
                $this->identicalTo($decryptionKey)
            )
            ->will($this->returnValue($preparedDecrypted));

        $decryptedValue = $this->decrypter->decryptValue($encryptedValue, $decryptionKey);

        $this->assertSame($preparedDecrypted, $decryptedValue);
    }

    /**
     * Create mock for DecrypterHandlerInterface.
     *
     * @return DecrypterHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createDecrypterHandlerInterfaceMock()
    {
        return $this->getMockBuilder(DecrypterHandlerInterface::class)->getMock();
    }
}
