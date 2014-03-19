<?php

namespace Bwlab\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Twig_Loader_String;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ViewModuleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('module:view:add')
            ->setDescription('Add a view to module')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'module name'
            )
            ->addOption(
                'viewname',
                null,
                InputOption::VALUE_REQUIRED,
                'View name'
            )->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'View type [hook, ...]',
                'hook'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //twig init
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/module');
        $twig = new Twig_Environment($loader, array());

        $fs = new Filesystem();
        //module name
        $name = strtolower($input->getOption('name'));

        // view name
        $viewname = strtolower($input->getOption('viewname'));

        //type of view
        $type = strtolower($input->getOption('type'));

        //init dir
        $dir = $this->getBaseDir($name);
        $indexphpfile = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'index.php';
        $output->writeln("create view " . $viewname . " in module " . $name);

        //config.xml
        try {
            $finder = new Finder();
            $finder->directories()->in($dir);

            //recupero la cartella view
            if (!count($finder->path('views'))) {
                $dir .= DIRECTORY_SEPARATOR . 'views';
                $fs->mkdir($dir );
                $fs->copy($indexphpfile, $dir.DIRECTORY_SEPARATOR.'index.php');
            };

            $d = $finder->directories()->name('templates');
            if (!count($d)) {
                $dir .= DIRECTORY_SEPARATOR . 'templates';
                $fs->mkdir($dir );
                $fs->copy($indexphpfile, $dir.DIRECTORY_SEPARATOR.'index.php');
            };



            switch ($type) {

                case 'hook':
                    $d =  $finder->directories()->name('hook')->notName('templates');
                    $hookdir = $this->getBaseDir($name). DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR .'templates'.DIRECTORY_SEPARATOR. 'hook';

                    if (!count($d)){
                        $fs->mkdir($hookdir);
                        $fs->copy($indexphpfile, $hookdir.DIRECTORY_SEPARATOR.'index.php');
                    }
                    $fs->touch($hookdir.DIRECTORY_SEPARATOR.$viewname.'.tpl');

                    $formatter = $this->getHelperSet()->get('formatter');

                    $formattedLine = $formatter->formatSection(
                        'to call view, add code in your module',
                        '      return $this->display(__FILE__, '.$viewname.'\'tpl\');'
                    );
                    $output->writeln($formattedLine);
                    break;
            }

        } catch (IOExceptionInterface $e) {
            $output->writeln('An error occurred while creating your module: ' . $e->getMessage());
        }


    }

    /**
     * @param $name
     * @return string
     */
    private function getBaseDir($name)
    {
        $dir = _PS_MODULE_DIR_ . $name;
        return $dir;
    }

}