<?php

declare(strict_types=1);

namespace addons\test\controller;

use think\addons\Controller;

class Index extends Controller
{
    public function index()
    {
        $welcome = get_addons_config_value('test', 'welcome', 'Hello Test');

        return $this->fetch('info', ['welcome' => $welcome]);
    }

    public function link()
    {
        $id = $this->request->param('id', 0, 'int');

        return $id > 0 ? 'hello link, id=' . $id : 'hello link';
    }
}