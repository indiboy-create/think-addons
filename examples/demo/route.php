<?php

declare(strict_types=1);

use think\facade\Route;

Route::get('demo/hello', function () {
    return 'hello demo';
});