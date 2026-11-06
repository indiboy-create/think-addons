<?php

declare(strict_types=1);

namespace addons\demo\service;

use addons\demo\contract\GreeterContract;

class Greeter implements GreeterContract
{
    public function greet(string $name = 'Demo'): string
    {
        return 'Hello ' . $name;
    }
}