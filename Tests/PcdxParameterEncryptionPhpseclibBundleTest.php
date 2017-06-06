<?php

/*
 * This file is part of the PcdxParameterEncryptionPhpseclibBundle package.
 *
 * (c) picodexter <https://picodexter.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Picodexter\ParameterEncryptionPhpseclibBundle\Tests;

use Picodexter\ParameterEncryptionPhpseclibBundle\PcdxParameterEncryptionPhpseclibBundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class PcdxParameterEncryptionPhpseclibBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSuccess()
    {
        $bundle = new PcdxParameterEncryptionPhpseclibBundle();

        $this->assertInstanceOf(BundleInterface::class, $bundle);
    }
}
