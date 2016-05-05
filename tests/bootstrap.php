<?php
die(getcwd());
include __DIR__ . "/../vendor/autoload.php";

$di = new Phalcon\DI\FactoryDefault();

Phalcon\DI::setDefault($di);