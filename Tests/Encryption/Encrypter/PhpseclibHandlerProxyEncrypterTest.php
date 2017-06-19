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

use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\Handler\EncrypterHandlerInterface;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Encrypter\PhpseclibHandlerProxyEncrypter;

class PhpseclibHandlerProxyEncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhpseclibHandlerProxyEncrypter
     */
    private $encrypter;

    /**
     * @var EncrypterHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * PHPUnit: setUp.
     */
    public function setUp()
    {
        $this->handler = $this->createEncrypterHandlerInterfaceMock();

        $this->encrypter = new PhpseclibHandlerProxyEncrypter($this->handler);
    }

    /**
     * PHPUnit: tearDown.
     */
    public function tearDown()
    {
        $this->encrypter = null;
        $this->handler = null;
    }

    public function testEncryptValueSuccess()
    {
        $plainValue = 'plain text';
        $encryptionKey = 'some key';
        $preparedEncrypted = 'encrypted value';

        $this->handler->expects($this->once())
            ->method('encryptValue')
            ->with(
                $this->identicalTo($plainValue),
                $this->identicalTo($encryptionKey)
            )
            ->will($this->returnValue($preparedEncrypted));

        $encryptedValue = $this->encrypter->encryptValue($plainValue, $encryptionKey);

        $this->assertSame($preparedEncrypted, $encryptedValue);
    }

    /**
     * Create mock for EncrypterHandlerInterface.
     *
     * @return EncrypterHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createEncrypterHandlerInterfaceMock()
    {
        return $this->getMockBuilder(EncrypterHandlerInterface::class)->getMock();
    }
}
