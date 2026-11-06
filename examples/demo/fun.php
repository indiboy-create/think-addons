<?php

declare(strict_types=1);

if (!function_exists('demo_hello')) {
    function demo_hello($name = 'Demo')
    {
        return 'Hello ' . $name;
    }
}