<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReferenceCommand extends BackstopCommand
{
    protected function configure()
    {
        $this
            ->setName('backstop:reference')
            ->setDescription('Create reference images for regressions tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->configPath();

        if (!is_file($configPath)) {
            $output->writeln('<error>Config not created. Run `./primer backstop:config` first</error>');
        } else {
            $this->startServer($output);

            // Run the reference test
            $this->runGulpCommand('reference');

            $this->stopServer();
        }
    }
}
