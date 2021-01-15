<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use Cocur\Slugify\Slugify;
use App\Form\ResgistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{

    /**
     * @Route("/register", name="account_register")
     */
    public function register (EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        $user = new User();

        // from est relié à l'instance $ad 
        $form = $this->createForm(ResgistrationType::class,$user);

        // extrait les données de la requete et les mets en concordance avec $ad
        $form->handleRequest($request);

        // si valide et bontoun "submit" cliké
        if ($form->isSubmitted() && $form->isValid() ) {

            $password = $user->getHash();
            $pass_encoded = $passwordEncoder->encodePassword($user,$password);
            $user->setHash($pass_encoded);

            $slugify = new Slugify();
            $slug=$slugify->slugify( $user->getFirstName().'-'.$user->getLastName() );
            $user -> setSlug($slug) ;


            $manager->persist($user);
            $manager->flush();

            //dump($ad->getId());
            $slug2 =$user->getSlug().'-'.$user->getId();
            $user->setSlug($slug2);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Merci '.$user->getFirstName().' votre profil a bien été enregistrée '
            );

            return $this->redirectToRoute('account_login');
        
        }

        return $this->render('account/register.html.twig', [   
            'form'=>$form->createView(),
        ]);

    }

    //modification du profil
    /**
    * @Route("/account/profile", name="account_profile")
    */
    public function profile (EntityManagerInterface $manager, Request $request) { 

        $user = $this->getUser();

        // from est relié à l'instance $ad 
        $form = $this->createForm(AccountType::class,$user);

        // extrait les données de la requete et les mets en concordance avec $ad
        $form->handleRequest($request);

        // si valide et bontoun "submit" cliké
        if ($form->isSubmitted() && $form->isValid() ) {

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le profil '.$user->getFirstName().' a bien été modifié '
            );

            return $this->redirectToRoute('account_index');

        }

        return $this->render('account/profile.html.twig', [   
            'form'=>$form->createView(),
            'user'=>$user ,
        ]);

    }


    /**
    * @Route("/account/", name="account_index")
    */
    public function myAccount () { 

        return $this->render('user/index.html.twig', [   
            'user'=>$this->getUser(),
        ]);

    }



    /**
    * @Route("/account/password-update", name="account_password")
    */
    public function updatePassword (EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $passwordEncoder) { 
        
        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate ;

        // from est relié à l'instance $passwordUpdate
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        // extrait les données de la requete et les mets en concordance avec $ad
        $form->handleRequest($request);

        // si valide et bontoun "submit" cliké
        if ($form->isSubmitted() && $form->isValid() ) {


            if ( !password_verify($passwordUpdate->getOldPassword(),$user->getHash()  ) ) {
                $this->addFlash(
                    'danger',
                    'Votre ancien mot de passe est incorrect'
                );
            }
            else {
                $newPassword = $passwordUpdate->getNewPassword();
                $pass_encoded = $passwordEncoder->encodePassword($user,$newPassword);
                $user->setHash($pass_encoded);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a bien été modifié'
                );
            
                return $this->redirectToRoute('account_index');
            }// END else


        }// END if validated

        return $this->render('account/password.html.twig', [
            'form'=>$form->createView(),   
        ]);

    } // END function updatePassword




    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('account/login.html.twig', [
            'loginError'         => $error,
        ]);
    }


    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {

    }
}
