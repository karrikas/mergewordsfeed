<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
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
