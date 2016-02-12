<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rareloop\Primer\Primer;

use Symfony\Component\Finder\Finder;

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

            // Update the list of patterns to test
            $finder = new Finder();
            $patterns = [];

            $sections = ['elements', 'components'];

            foreach ($sections as $section) {
                $children = $finder->directories()->depth('== 1')->in(Primer::$PATTERN_PATH . '/' . $section);

                foreach ($children as $child) {
                    $patterns[] = trim(str_replace(PRIMER::$PATTERN_PATH, '', $child->getRealPath()), '/');
                }
            }

            $json = json_encode($patterns, JSON_PRETTY_PRINT);
            file_put_contents(Primer::$BASE_PATH . '/backstop/urls.json', $json);

            // Create a hash
            $hash = md5($json);

            // Update the backstop.config.js file with the hash
            $configPath = PRIMER::$BASE_PATH . '/backstop.config.js';
            $config = preg_replace('/var hash = \'[a-z0-9]*\'/', 'var hash = \'' . $hash . '\'', file_get_contents($configPath));
            file_put_contents($configPath, $config);

            // Run the reference test
            $this->runGulpCommand('reference');

            $this->stopServer();
        }
    }
}
