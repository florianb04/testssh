<?php

namespace App\Services;

use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeleteImageServices extends AbstractController
    {
        public function deleteImageServices ($ad,$manager) {
    
            //suppréssion de la première virgules des images uploadées
            $tabid = $ad->tableau_id ;
            $tabid = preg_replace('#^,#','',$tabid);
            $tabid = explode(',',$tabid);

            // on boucle sur charge id d'image clické
            foreach ($tabid as $id) {

                //une fois dans chaque image on cherche dans leurs propriétés
                foreach ($ad->getImageUploads() as $image) {

                    //si l'id d'une image correspond à l'id receptionne quand cliké alors
                    if ($id == $image ->getId() ) {

                        $manager->remove($image);
                        $manager->flush();

                        // on prend le $_SERVER['DOCUMENT_ROOT'] pour avoir le chemin absolu de l'emplacement en dur de mes images
                        // on concatener avec l'url de l'image
                        unlink($_SERVER['DOCUMENT_ROOT'].$image->getUrl() );
                    }
                }
            }

            // dump($_SERVER); 
            // exit;
        }

    }