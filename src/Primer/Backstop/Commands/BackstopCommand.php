<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Command\Command;
use Rareloop\Primer\Primer;

class BackstopCommand extends Command
{
    private $serverPID;

    protected function startServer($output)
    {
        $this->serverPID = shell_exec("nohup php -S 0.0.0.0:9000 " . __DIR__ . "/../../../server.php > /dev/null 2> /dev/null & echo $!");
        $output->writeln('<info>Temporary server created http://localhost:9000</info>');
    }

    protected function stopServer()
    {
        exec("kill {$this->serverPID}");
    }

    protected function configPath()
    {
        return Primer::$BASE_PATH . '/backstop.config.js';
    }

    protected function runGulpCommand($command)
    {
        system('cd ' . __DIR__ . '/../../node_modules/backstopjs && gulp ' . $command. ' --backstopConfigFilePath=' . $this->configPath());
    }
}
