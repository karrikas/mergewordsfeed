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
use AppBundle\Entity\Message;

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
     * @Route("/mixer", name="mixer")
     */
    public function mixerAction(Request $request)
    {
        if ($request->isMethod('post')) {
            $group = $request->get('group');
            $mix = $request->get('mix');

            $this->mix($group, $mix);

            return $this->redirectToRoute('mixer');
        }

        return $this->render('default/mixer.html.twig');
    }

    /**
     * @Route("/messages/delete", name="messages-delete")
     */
    public function messagesDeleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();

        $group = $request->get('group');
        $group = urldecode($group);

        if (!$group) {
            throw new \Exception("Group not found");
        }

        $messages = $em->getRepository('AppBundle:Message')->findBy(
            ['groupName' => $group],
            ['id' => 'desc']
        );

        foreach ($messages as $message) {
            $em->remove($message);
        }
        $em->flush();

        return $this->redirectToRoute('messages');
    }

    /**
     * @Route("/messages", name="messages")
     */
    public function messagesAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $group = $em->getRepository('AppBundle:Message')
            ->createQueryBuilder('m')
            ->select('DISTINCT m.groupName')
            ->getQuery();

        $groups = $group->getResult();

        $group = $request->get('group');
        $group = urldecode($group);

        $filter = [];
        if (!empty($group)) {
            $filter = ['groupName' => $group];
        }

        $messages = $em->getRepository('AppBundle:Message')->findBy(
            $filter,
            ['id' => 'desc']
        );

        $paginator  = $this->get('knp_paginator');
        $messages = $paginator->paginate(
            $messages, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('default/messages.html.twig', [
            'selected' => $group,
            'groups' => $groups,
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/oauth", name="oauth")
     */
    public function oauthrAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $twitterService = $this->get('app.twitter');

        $request_token = [];
        $request_token['oauth_token'] = $_SESSION['oauth_token'];
        $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

        if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
            throw new \Exception("Twitter Connection error");
            
        }

        $twitterConnection = $twitterService->getConnection(
            $request_token['oauth_token'],
            $request_token['oauth_token_secret']
        );

        $access_token = $twitterConnection->oauth("oauth/access_token", [
            "oauth_verifier" => $_REQUEST['oauth_verifier']
        ]);

        $tc = new TwitterConnect();
        $tc->setAccessToken($access_token['oauth_token']);
        $tc->setAccessTokenSecret($access_token['oauth_token_secret']);
        $tc->setUserId($access_token['user_id']);
        $tc->setScreenName($access_token['screen_name']);
        $tc->setXAuthExpires($access_token['x_auth_expires']);

        $em->persist($tc);
        $em->flush();

        return $this->redirectToRoute('connection_to_twitter');
    }

    /**
     * @Route("/connection", name="connection_to_twitter")
     */
    public function connectio2twitterAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $twitterService = $this->get('app.twitter');

        $twitterConnection = $twitterService->getConnection();
        $url = $twitterService->getAuthorizeUrl($twitterConnection);

        $twitterConnect = $em->getRepository('AppBundle:TwitterConnect')->findAll();

        return $this->render('default/connection-to-twitter.html.twig', [
            'twitterConnect' => $twitterConnect,
            'twitterConnectUrl' => $url
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

    protected function mix($group, $value)
    {
        $em = $this->get('doctrine')->getManager();

        if (empty($group)) {
            $group = 'group-'.date('YmdHis');
        }

        $haveGroup = false;

        while (!$haveGroup) {
            $messages = $em->getRepository('AppBundle:Message')->findBy(['groupName' => $group]);
            if (count($messages) == 0) {
                $haveGroup = true;
            } else {
                $group .= '-'.date('YmdHis');
            }
        }

        $info = [];
        foreach ($value as $key => $mix) {
            $info[] = explode("\n", $mix);
        }

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
            $message = new Message();
            $message->setGroupName($group);
            $message->setMessage($s);
            $em->persist($message);
        }
        $em->flush();
    }
}
