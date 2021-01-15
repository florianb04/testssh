<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager)
    {


    	$users = $manager->createQuery("SELECT count(u) FROM App\Entity\User u");
    	$users=$users->getSingleScalarResult();

    	$ads = $manager->createQuery("SELECT count(a) FROM App\Entity\Ad a")->getSingleScalarResult();
		$bookings= $manager->createQuery("SELECT count(b) FROM App\Entity\Booking b")->getSingleScalarResult();
		$comments = $manager->createQuery("SELECT count(c) FROM App\Entity\Comment c")->getSingleScalarResult();

		$bestAds=$manager->createQuery(
			'SELECT AVG(c.rating) as note,a.title,a.id,u.firstName,u.picture  FROM App\Entity\Comment c
			JOIN c.ad a
			JOIN a.author u
			GROUP BY a
			ORDER BY note DESC')->setMaxresults(5)->getResult();

//dump($bestAds);

        return $this->render('admin/dashboard/index.html.twig', [
           'users'=>$users,
           'ads'=>$ads, 
           'bookings'=>$bookings, 
           'comments'=>$comments, 
           'bestAds'=>$bestAds
        ]);
    }
}


