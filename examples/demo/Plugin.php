<?php

declare(strict_types=1);

namespace addons\demo;

use think\Addons;

class Plugin extends Addons
{
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function demoContent($params)
    {
        return '<div class="demo-hook">demo hook</div>';
    }
}