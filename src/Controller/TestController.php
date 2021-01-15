<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {

    		$a='wael';

   $tab = ['eric'=>52,'Gerald'=>53,'florian'=>26];

   dump($tab);
   dump($a);
   dump($this);

        return $this->render('test/test.html.twig', [
            	'prenom'=>$a,
            'tableau'=>$tab,
            'age'=>13,
        ]);
    }
}
