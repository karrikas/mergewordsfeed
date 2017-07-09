<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TwitterMessagePostCommand extends ContainerAwareCommand
{
    protected function configure()
    {
         $this
            ->setName('twitter:message:post')
            ->setDescription('Post message to twitter from messages.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitterService = $this->getContainer()->get('app.twitter');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $twitterConnect = $em->getRepository('AppBundle:TwitterConnect')
            ->findAll();

        if (!$twitterConnect) {
            throw new \Exception("No connection to twitter found.");
            
        }
        shuffle($twitterConnect);
        $twitterConnect = $twitterConnect[0];

        $message = $em->getRepository('AppBundle:Message')->findOneBy([
            'used' => false
        ]);

        if (!$message) {
            throw new \Exception("No message found");
        }

        $twitterService->post(
            $twitterConnect->getAccessToken(),
            $twitterConnect->getAccessTokenSecret(),
            $message->getMessage()
        );

        $message->setUsed(true);
        $em->persist($message);
        $em->flush();

        $output->writeln($twitterConnect->getId().' > '.$message->getMessage());
    }
}
