<?php

declare(strict_types=1);

namespace addons\test;

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

    public function testhook($params)
    {
        return '<div class="test-hook">test hook</div>';
    }
}