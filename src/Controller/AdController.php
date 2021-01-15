<?php

namespace App\Controller;

use App\Entity\Ad;
// use App\Entity\Image;
// use App\Entity\ImageUpload;
use App\Form\AnnonceType;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use App\Services\DeleteImageServices;
use App\Services\ImagesUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo) // (AdRepository $repo) fait la même chose que la ligne du dessous
    {

    	//$repo=$this->getDoctrine()->getRepository(Ad::class); // on va chercher le repository de Ad

    	$ads=$repo->findAll();// prend tous les enregistrements de la table visée.

    	//dump($ads);

        return $this->render('ad/index.html.twig', [
            'ads'=>$ads, // je transmet à twig une clé ads qui contient $ads
        ]);
    }


    /**
    * @Route("/ads/new", name="ads_create")
    *
    * @IsGranted("ROLE_USER")
    */
    public function create(EntityManagerInterface $manager, Request $request, ImagesUploadService $upload)
    {
        $ad = new Ad();

        $ad -> setAuthor( $this->getUser() ) ;

        // from est relié à l'instance $ad 
        $form = $this->createForm(AnnonceType::class,$ad);

        // extrait les données de la requete et les met en concordance avec $ad
        $form->handleRequest($request);

        // si valide et bontoun "submit" cliké
        if ($form->isSubmitted() && $form->isValid() ) {

            $slugify=new Slugify();
            $slug=$slugify->slugify($ad->getTitle() );
            // on ajoute le slug créé dans l'instance $ad
            $ad->setSlug($slug);


            // image avec lien URL 
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
                dump($ad->getImages()) ;
            }

            // gestion des images uploadé
            $upload -> upload($ad,$manager);


            $manager->persist($ad);
            $manager->flush(); // enregistrer 1er fois pour avoir une auto-increment de l'id dans la BDD

            // réécriture du slug avec l'id créé
            $slug2 =$ad->getSlug().'_'.$ad->getId();
            $ad->setSlug($slug2);

            // dernier enregistrement finale
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'l\'annonce de titre : '.$ad->getTitle().' a bien été en registrée '
            );

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
            
        }


        return $this->render('ad/new.html.twig', [   
            'form'=>$form->createView(),
        ]);

    }

    
    /**
    * @Route("/ads/{slug}/edit", name="ads_edit")
    * 
    * @Security ("is_granted('ROLE_USER') and user == ad.getAuthor()" ,  message ="Cette annonce ne vous appartient pas")
    */
    public function edit(EntityManagerInterface $manager, Request $request,Ad $ad, ImagesUploadService $upload, DeleteImageServices $delete)
    {

        // from est relie à l'instance $ad 
        $form = $this->createForm(AnnonceType::class,$ad);

        // extrait les données de la requete et les meten concordance avec $ad
        $form->handleRequest($request);

        // si valide et bontoun "submit" cliké
        if ($form->isSubmitted() && $form->isValid() ) {

            // gestion des images uploadé
            $upload -> upload($ad,$manager);

            // supression des images
            $delete -> deleteImageServices($ad,$manager);

            $slugify=new Slugify();
            $slug=$slugify->slugify($ad->getTitle() );

            // on ajoute le slug créé dans l'instance $ad
            $ad->setSlug($slug);

            // dump($ad); exit;

            foreach ($ad->getImages() as $image) {

                $image->setAd($ad);

                $manager->persist($image);

                dump($ad->getImages()) ;


            }

            $manager->persist($ad);
            $manager->flush(); // enregistrer 1er fois pour avoir une auto-increment de l'id dans la BDD

            // réécriture du slug avec l'id créé
            $slug2 =$ad->getSlug().'_'.$ad->getId();
            $ad->setSlug($slug2);

            // dernier enregistrement finale
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'l\'annonce de titre : '.$ad->getTitle().' a bien été enregistrée '
            );

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
            
        }

        return $this->render('ad/edit.html.twig', [   
            'form'=>$form->createView(),
            'ad'=>$ad,
        ]);
    } // FIN fonction edit


    /**
    * @Route("/ads/{slug}", name="ads_show")
    */
    public function show(Ad $ad)
    {
        // $ad = $repo->findOneBySlug($slug);
        // dump($ad);

        return $this->render('ad/show.html.twig', [
            'ad'=>$ad,

        ]);
    }


    /**
    * @Route("/ads/{slug}/delete", name="ads_delete")
    * 
    * @Security ("is_granted('ROLE_USER') and user == ad.getAuthor()" ,  message ="Vous ne pouvez pas supprimer cette annonce")
    */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            'l\'annonce  '.$ad->getTitle().' a bien été supprimée '
        );

        return $this->redirectToRoute('ads_index');
    }

}




