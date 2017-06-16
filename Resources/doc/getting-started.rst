Getting Started
===============

Prerequisites
-------------

You need Symfony 2.7+ with `PcdxParameterEncryptionBundle`_ already installed
and enabled (please refer to its own documentation).

Installation
------------

Step 1: Download the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: terminal

    $ composer require picodexter/parameter-encryption-phpseclib-bundle "~1"

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding it to the list of registered bundles
in the ``app/AppKernel.php`` file of your project:

.. code-block:: php

    <?php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Picodexter\ParameterEncryptionPhpseclibBundle\PcdxParameterEncryptionPhpseclibBundle(),
            );

            // ...
        }

        // ...
    }

Step 3: Configuration
~~~~~~~~~~~~~~~~~~~~~

You can now use the following services in the PcdxParameterEncryptionBundle configuration:

* encrypter:

  * symmetric ciphers:

    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.3des``
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.aes.KEY_LENGTH`` where ``KEY_LENGTH`` is
      the key length and can be 128, 192 or 256
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.blowfish``
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.des``
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.rc2``
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.rc4``
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.rijndael.KEY_LENGTH`` where ``KEY_LENGTH`` is
      the key length and can be 128, 160, 192, 224 or 256
    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.twofish``

  * asymmetric ciphers:

    * ``pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.rsa``

* decrypter:

  * symmetric ciphers:

    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.3des``
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.aes.KEY_LENGTH`` where ``KEY_LENGTH`` is
      the key length and can be 128, 192 or 256
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.blowfish``
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.des``
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.rc2``
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.rc4``
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.rijndael.KEY_LENGTH`` where ``KEY_LENGTH`` is
      the key length and can be 128, 160, 192, 224 or 256
    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.twofish``

  * asymmetric ciphers:

    * ``pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.rsa``

Example:

1. Application configuration:

    .. configuration-block::

        .. code-block:: yaml

            # app/config/config.yml
            pcdx_parameter_encryption:
                algorithms:
                    -   id: 'phpseclib_aes_256'
                        pattern:
                            type: 'value_prefix'
                            arguments:
                                -   '=#!PPE!psl:aes:256!#='
                        encryption:
                            service: 'pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.aes.256'
                            key: '%parameter_encryption.phpseclib.aes.256.key%'
                        decryption:
                            service: 'pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.aes.256'
                            key: '%parameter_encryption.phpseclib.aes.256.key%'

        .. code-block:: xml

            <!-- app/config/config.xml -->
            <?xml version="1.0" encoding="UTF-8" ?>
            <container xmlns="http://symfony.com/schema/dic/services"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:ppe="https://picodexter.io/schema/dic/pcdx_parameter_encryption"
                xsi:schemaLocation="https://picodexter.io/schema/dic/pcdx_parameter_encryption
                    https://picodexter.io/schema/dic/pcdx_parameter_encryption/pcdx_parameter_encryption-1.0.xsd">

                <ppe:config>
                    <ppe:algorithm id="phpseclib_aes_256">
                        <ppe:pattern type="value_prefix">
                            <ppe:argument>=#!PPE!psl:aes:256!#=</ppe:argument>
                        </ppe:pattern>
                        <ppe:encryption service="pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.aes.256"
                            key="%parameter_encryption.phpseclib.aes.256.key%" />
                        <ppe:decryption service="pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.aes.256"
                            key="%parameter_encryption.phpseclib.aes.256.key%" />
                    </ppe:algorithm>
                </ppe:config>
            </container>

        .. code-block:: php

            // app/config/config.php
            $container->loadFromExtension(
                'pcdx_parameter_encryption',
                [
                    'algorithms' => [
                        [
                            'id' => 'phpseclib_aes_256',
                            'pattern' => [
                                'type' => 'value_prefix',
                                'arguments' => ['=#!PPE!psl:aes:256!#='],
                            ],
                            'encryption' => [
                                'service' => 'pcdx_parameter_encryption_phpseclib.encryption.encrypter.phpseclib.aes.256',
                                'key' => '%parameter_encryption.phpseclib.aes.256.key%',
                            ],
                            'decryption' => [
                                'service' => 'pcdx_parameter_encryption_phpseclib.encryption.decrypter.phpseclib.aes.256',
                                'key' => '%parameter_encryption.phpseclib.aes.256.key%',
                            ],
                        ],
                    ],
                ]
            );

2. Parameters:

    .. configuration-block::

        .. code-block:: yaml

            # app/config/parameters.yml
            parameters:
                parameter_encryption.phpseclib.aes.256.key: 'YOUR_ENCRYPTION_KEY'

        .. code-block:: xml

            <!-- app/config/parameters.xml -->
            <?xml version="1.0" encoding="UTF-8" ?>
            <container xmlns="http://symfony.com/schema/dic/services"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://symfony.com/schema/dic/services
                    http://symfony.com/schema/dic/services/services-1.0.xsd">

                <parameters>
                    <parameter key="parameter_encryption.phpseclib.aes.256.key">YOUR_ENCRYPTION_KEY</parameter>
                </parameters>
            </container>

        .. code-block:: php

            // app/config/parameters.php
            $container->setParameter('parameter_encryption.phpseclib.aes.256.key', 'YOUR_ENCRYPTION_KEY');

.. _PcdxParameterEncryptionBundle: https://github.com/picodexter/PcdxParameterEncryptionBundle
.. _phpseclib/phpseclib: https://github.com/phpseclib/phpseclib
.. _installation chapter: https://getcomposer.org/doc/00-intro.md
