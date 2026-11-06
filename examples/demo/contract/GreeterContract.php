<?php

declare(strict_types=1);

namespace addons\demo\contract;

interface GreeterContract
{
    public function greet(string $name = 'Demo'): string;
}