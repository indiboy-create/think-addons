<?php

declare(strict_types=1);

namespace addons\demo\controller;

use addons\demo\contract\GreeterContract;
use think\addons\Controller;

class Index extends Controller
{
    public function index()
    {
        $welcome  = get_addons_config_value('demo', 'welcome', 'Hello Demo');
        $greeting = app(GreeterContract::class)->greet('Demo');

        $this->assign([
            'welcome'  => $welcome,
            'greeting' => $greeting,
            'hello'    => demo_hello('Demo'),
        ]);

        return $this->fetch('index');
    }

    public function link()
    {
        $id = $this->request->param('id', 0, 'int');

        $this->assign('id', $id);

        return $this->fetch('link');
    }
}