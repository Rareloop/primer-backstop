<?php namespace Rareloop\Primer\Backstop\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Rareloop\Primer\Primer;

use Symfony\Component\Finder\Finder;

class ReferenceCommand extends BackstopCommand
{
    protected function configure()
    {
        $this
            ->setName('backstop:reference')
            ->setDescription('Create reference images for regressions tests')
            ->addOption(
                'elements',
                'e',
                InputOption::VALUE_NONE,
                'Add elements to the list of reference images.'
            )
            ->addOption(
                'components',
                'c',
                InputOption::VALUE_NONE,
                'Add components to the list of reference images.'
            )
            ->addOption(
                'templates',
                't',
                InputOption::VALUE_NONE,
                'Add templates to the list of reference images.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->configPath();

        if (!is_file($configPath)) {
            $output->writeln('<error>Config not created. Run `./primer backstop:config` first</error>');
        } else {
            $this->startServer($output);

            // Update the list of patterns to test
            $patterns = [];

            $sections = ['elements', 'components'];

            $cliSections = [];

            if ($input->getOption('elements')) {
                $cliSections[] = 'elements';
            }

            if ($input->getOption('components')) {
                $cliSections[] = 'components';
            }

            if ($input->getOption('templates')) {
                $cliSections[] = 'templates';
            }

            // If any options have been provided on the CLI then use just these options
            if (!empty($cliSections)) {
                $sections = $cliSections;
            }


            foreach ($sections as $section) {
                $finder = new Finder();
                $depth = $section === 'templates' ? '==0' : '==1';
                $children = $finder->directories()->depth($depth)->in(Primer::$PATTERN_PATH . '/' . $section);

                foreach ($children as $child) {
                    $prefix = $section === 'templates' ? '' : 'patterns/';
                    $patterns[] = $prefix . trim(str_replace(PRIMER::$PATTERN_PATH, '', $child->getRealPath()), '/');
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
