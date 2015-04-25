<?php

/*
 * This file is part of the MockMaker package.
 *
 * (c) Evan Johnson <crimsonminion@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('MockMaker\\', __DIR__ . '/MockMaker');

date_default_timezone_set('UTC');
