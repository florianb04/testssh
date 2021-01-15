<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Count;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index", requirements={"page"="[0-9]{1,}"} )
     */
    public function index(AdRepository $repo , $page=1)
    {

        $limit = 5 ; //on veux 5 enregistrements par page

        $start = ($limit * $page) - $limit ; //calcul de l'offset

        $total = count($repo->findAll()) ; // nombres total d'annonces

        $pages = ceil($total / $limit) ; //arrondi à l'entier supérieur pour le nbrs total de pages

        
        // $ads=$repo->findAll();// prend tous les enregistrements de la table visée.

        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findBy([],[],$limit,$start),
            'page'=>$page,
            'pages'=>$pages,
        ]);
    }
}
