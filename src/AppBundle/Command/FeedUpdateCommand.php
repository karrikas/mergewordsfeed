<?php
// src/AppBundle/Command/CreateUserCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class FeedUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
         $this
            // the name of the command (the part after "bin/console")
            ->setName('feed:update')
            ->addArgument('feed_number', InputArgument::OPTIONAL)
            ->setDescription('Update feed.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $varPath = $this->getContainer()->get('kernel')->getRootDir().'/var/';
        $twetPath = $varPath.'twet/';
        $feedPath = $varPath.'feed/';
        $files = scandir($twetPath);

        if (!isset($files[3])) {
            $output->writeln('No files found.');
        }
        $file = $files[3];
        $filePath = $twetPath.$file;

        $number = $input->getArgument('feed_number');
        if (empty($number)) {
            $number = 1;
        }

        $feedPath .= $number.'/';
        if (!file_exists($feedPath)) {
            mkdir($feedPath);
        }
        rename($filePath, $feedPath.$file);
        $output->writeln($file);
    }
}
