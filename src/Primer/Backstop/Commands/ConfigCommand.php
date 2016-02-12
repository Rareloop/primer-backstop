<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            copy(__DIR__ . '/../../backstop.config.js', $this->configPath());
            $output->writeln('<info>Config created: ' . $this->configPath() . '</info>');
        } else {
            $output->writeln('<error>Config already exists</error>');
        }
    }
}
