<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

(new Whitesmoke\Foundation\Application(dirname(__DIR__)))->run();
