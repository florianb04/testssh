<?php

namespace App\Services;

use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImagesUploadService extends AbstractController
    {
        public function upload ($ad,$manager) {

            
            foreach ($ad->file as $file) {

                // tech 1 : marche pas si point dans le nom de fichier autre que l'extention
                // $position_point=strpos($file->getClientOriginalName(),'.');
                // $original_name=substr($file->getClientOriginalName(),0,$position_point);
               
                // $original_name = preg_replace('#\.(jpg|png|gif)$#','',$file->getClientOriginalName());
                $original_name = preg_replace('#\.[a-zA-Z0-9]*$#','',$file->getClientOriginalName());
  
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $upload = new ImageUpload();
                $upload -> setAd($ad)
                        -> setName($original_name)
                        -> setUrl('/uploads/'.$fileName);

                $manager -> persist($upload);
                $file -> move($this->getParameter('images_directory'),$fileName);

            }
        }

    }