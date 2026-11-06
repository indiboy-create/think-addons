<?php

declare(strict_types=1);

return [
    'welcome' => [
        'title'  => '欢迎语',
        'type'   => 'string',
        'value'  => 'Hello Demo',
        'rule'   => 'required',
        'msg'    => '',
        'tip'    => '',
        'ok'     => '',
        'extend' => '',
    ],
    'rewrite' => [
        'title'  => '伪静态',
        'type'   => 'array',
        'value'  => [
            // 键 = URL 规则模板，值 = 目标地址「插件/控制器/操作」
            '/demo'           => 'demo/index/index',
            '/demo/link'      => 'demo/index/link',
            '/demo/link/<id>' => 'demo/index/link',
        ],
        'rule'   => 'required',
        'msg'    => '',
        'tip'    => '',
        'ok'     => '',
        'extend' => '',
    ],
];