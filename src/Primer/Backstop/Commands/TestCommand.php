<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BackstopCommand
{
    protected function configure()
    {
        $this
            ->setName('backstop:test')
            ->setDescription('Tests for regressions tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->configPath();

        if (!is_file($configPath)) {
            $output->writeln('<error>Config not created. Run `./primer backstop:config` first</error>');
        } else {
            $this->startServer($output);

            // Run the reference test
            $this->runGulpCommand('test');

            $this->stopServer();
        }
    }
}
