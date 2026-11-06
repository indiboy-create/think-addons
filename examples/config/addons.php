<?php

declare(strict_types=1);

return [
    'autoload' => true,
    'hooks'    => [],
    'route'    => [],
    'service'  => [],

    'jump' => [
        // 绝对路径；留空使用包内 src/addons/tpl/dispatch_jump.tpl
        // 例如：root_path() . 'app/index/view/public/jump_success.tpl'
        'dispatch_success_tmpl' => '',
        'dispatch_error_tmpl'   => '',
    ],
];