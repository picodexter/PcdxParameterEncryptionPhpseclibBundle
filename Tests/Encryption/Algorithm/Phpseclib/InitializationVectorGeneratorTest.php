<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\Encryption\Algorithm\Phpseclib;

use phpseclib\Crypt\Base as BaseCipher;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\InitializationVectorGenerator;
use Picodexter\ParameterEncryptionPhpseclibBundle\Encryption\Algorithm\Phpseclib\RandomStringGeneratorInterface;

class InitializationVectorGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateInitializationVectorSuccess()
    {
        $blockLength = 16;
        $preparedString = 'generated string';

        $stringGenerator = $this->createRandomStringGeneratorInterfaceMock();

        $generator = new InitializationVectorGenerator($stringGenerator);

        $cipher = $this->createBaseCipherMock();

        $cipher->expects($this->once())
            ->method('getBlockLength')
            ->with()
            ->will($this->returnValue($blockLength));

        $stringGenerator->expects($this->once())
            ->method('generateRandomString')
            ->with($this->identicalTo((int) floor($blockLength / 8)))
            ->will($this->returnValue($preparedString));

        $generatedString = $generator->generateInitializationVector($cipher);

        $this->assertSame($preparedString, $generatedString);
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
     * Create mock for RandomStringGeneratorInterface.
     *
     * @return RandomStringGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRandomStringGeneratorInterfaceMock()
    {
        return $this->getMockBuilder(RandomStringGeneratorInterface::class)->getMock();
    }
}
