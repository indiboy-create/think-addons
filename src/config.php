<?php

declare(strict_types=1);

return [
    'autoload' => true,
    'hooks'    => [],
    'route'    => [],
    'service'  => [],

    // 跳转提示模板（绝对路径），留空使用内置模板
    'jump' => [
        'dispatch_success_tmpl' => '',
        'dispatch_error_tmpl'   => '',
    ],
];