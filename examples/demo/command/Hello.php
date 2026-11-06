<?php

declare(strict_types=1);

namespace addons\demo\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Hello extends Command
{
    protected function configure()
    {
        $this->setName('demo:hello')
            ->setDescription('Demo command');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('hello demo');
    }
}