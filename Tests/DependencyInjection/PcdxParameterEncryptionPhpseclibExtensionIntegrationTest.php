<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests\DependencyInjection;

use Picodexter\ParameterEncryptionPhpseclibBundle\DependencyInjection\PcdxParameterEncryptionPhpseclibExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PcdxParameterEncryptionPhpseclibExtensionIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadSuccess()
    {
        $mergedConfig = [];
        $container = new ContainerBuilder();

        $extension = new PcdxParameterEncryptionPhpseclibExtension();

        $extension->load($mergedConfig, $container);

        $serviceDefinition = $container
            ->getDefinition('pcdx_parameter_encryption_phpseclib.encryption.algorithm.phpseclib.cipher.template.3des');

        $this->assertInstanceOf(Definition::class, $serviceDefinition);
    }
}
