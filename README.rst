CMSilex, a simple CMS based on Silex
====================================

View the documentation on `Read the docs <http://cmsilex-docs.readthedocs.io/>`_.

CMSilex is a CMS based on Silex, the PHP micro-framework::

    <?php

    require_once __DIR__.'/../vendor/autoload.php';

    $app = new \CMSilex\Application();

    $app->run();

CMSilex works with PHP 5.5.9 or later.

Installation
------------

The recommended way to install CMSilex is through `Composer`_:

.. code-block:: bash

    composer require cmsilex/cmsilex "dev-master"

License
-------

CMSilex is licensed under the MIT license.

.. _Composer:           http://getcomposer.org
