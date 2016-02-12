<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rareloop\Primer\Primer;

class ConfigCommand extends BackstopCommand
{
    protected function configure()
    {
        $this
            ->setName('backstop:config')
            ->setDescription('Create regression test config');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->configPath();

        if (!is_file($configPath)) {
            copy(__DIR__ . '/../../../../backstop.config.js', $this->configPath());
            $output->writeln('<info>Config created: ' . $this->configPath() . '</info>');

            // Also add the base Casper scripts we can overwrite
            if (!is_dir(Primer::$BASE_PATH . '/backstop')) {
                @mkdir(Primer::$BASE_PATH . '/backstop');
            }

            if (!is_dir(Primer::$BASE_PATH . '/backstop/casper_scripts')) {
                @mkdir(Primer::$BASE_PATH . '/backstop/casper_scripts');
            }

            copy(__DIR__ . '/../../../../casper_scripts/onBefore.js', Primer::$BASE_PATH . '/backstop/casper_scripts/onBefore.js');
            copy(__DIR__ . '/../../../../casper_scripts/onReady.js', Primer::$BASE_PATH . '/backstop/casper_scripts/onReady.js');

        } else {
            $output->writeln('<error>Config already exists</error>');
        }
    }
}
