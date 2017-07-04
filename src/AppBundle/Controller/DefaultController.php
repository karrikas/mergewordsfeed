<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\TwitterConnect;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/feed/{number}.xml", defaults={"_format"="xml"}, name="feed")
     */
    public function feedAction(Request $request, $number)
    {
        $feedPath = $this->get('kernel')->getRootDir().'/var/feed/'.$number.'/';
        $ignored = array('.', '..');

        $files = array();
        foreach (scandir($feedPath) as $file) {
            if (in_array($file, $ignored)) {
                continue;
            }
            $files[$file] = filectime($feedPath.'/'.$file);
        }

        arsort($files);
        $files = array_slice($files, 0, 10);

        $feed = [];
        foreach ($files as $key => $date) {
            $info = file_get_contents($feedPath.$key);
            $feed[] = [
                'date' => date('r', $date),
                'content' => $info,
                'md5' => md5($date),
            ];
        }

        return $this->render('default/feed.xml.twig', [
            'feed' => $feed,
            'feedId' => $number,
        ]);
    }

    /**
     * @Route("/mixer", name="mixer")
     */
    public function mixerAction(Request $request)
    {
        if ($request->isMethod("post")) {
            $mix = $request->get('mix');
            $this->mix($mix);

            return $this->redirectToRoute('mixer');
        }

        return $this->render('default/mixer.html.twig');
    }

    /**
     * @Route("/connection", name="connection_to_twitter")
     */
    public function connectio2twitterAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $tc = new TwitterConnect();


        $form = $this->createFormBuilder($tc)
            ->add('access_token', TextType::class)
            ->add('access_token_secret', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();

         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tc = $form->getData();
            $em->persist($tc);
            $em->flush();

            return $this->redirectToRoute('connection_to_twitter');
        }

        $twitterConnect = $em->getRepository('AppBundle:TwitterConnect')->findAll();

        return $this->render('default/connection-to-twitter.html.twig', [
            'form' => $form->createView(),
            'twitterConnect' => $twitterConnect,
        ]);
    }

    /**
     * @Route("/connection/delete/{id}", name="connection_to_twitter_delete")
     */
    public function connectio2twitterDeleteAction(Request $request, $id)
    {
        $em = $this->get('doctrine')->getManager();
        $connection = $em->getRepository('AppBundle:TwitterConnect')->find($id);
        $em->remove($connection);
        $em->flush();

        return $this->redirectToRoute('connection_to_twitter');
    }

    /**
     * @Route("/connection/test", name="connection_to_twitter_test")
     */
    public function connectio2twitterTestAction(Request $request)
    {
        $accessToken = $request->get('access_token');
        $accessTokenSecret = $request->get('access_token_secret');

        $test = $this->get('app.twitter')->testConnection($accessToken, $accessTokenSecret);

        return new JsonResponse($test);
    }

    protected function mix($value)
    {
        $info = [];
        foreach ($value as $key => $mix) {
            $info[] = explode("\n", $mix);
        }

        $varPath = $this->get('kernel')->getRootDir().'/var/twet/';

        $str = [];
        for ($i=0; $i<count($info[0]); $i++) {
            for ($a=0; $a<count($info[1]); $a++) {
                for ($b=0; $b<count($info[2]); $b++) {
                    for ($c=0; $c<count($info[3]); $c++) {
                        $str[] = sprintf('%s %s %s %s', trim($info[0][$i]), trim($info[1][$a]), trim($info[2][$b]), trim($info[3][$c]));
                    }
                }
            }
        }

        foreach ($str as $key => $s) {
            file_put_contents($varPath.md5($s).'.txt', $s);
        }
    }
}
