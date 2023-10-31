<?php

namespace App\Controller;

use App\Service\MessagerieService;
use App\Service\UtilsService;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    private MessagerieService $messagerie;

    public function __construct(UserRepository $userRepository,EntityManagerInterface $em, MessagerieService $messagerie)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->messagerie = $messagerie;
    }

    #[Route('/register', name: 'app_register')]
    public function addUser( UserPasswordHasherInterface $userPasswordHasher, Request $request): Response
    {
        $msg = '';
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->userRepository->findOneBy(['email'=> $form->get("email")->getData()])) {
                $msg = "L'utilisateur existe déjà !";

            } else {
                //Nettoyage et enregistrement du password
                $pass = UtilsService::cleanInput($request->request->all('register')['password']['first']);
                $hash = $userPasswordHasher->hashPassword($user, $pass);

                //Nettoyage setters email, firstName & name
                $user->setEmail(UtilsService::cleanInput($request->request->all('register')['email']));
                $user->setFirstName(UtilsService::cleanInput($request->request->all('register')['firstName']));
                $user->setName(UtilsService::cleanInput($request->request->all('register')['name']));
                
                $user->setPassword($hash);
                $user->setRoles(['ROLE_USER']);
                $user->setActivated(false);

                $this->em->persist($user);
                $this->em->flush();
                $msg = "Le compte: ".$user->getEmail()." a bien été ajouté en BDD";
                
                if ($msg === "Le compte: ".$user->getEmail()." a bien été ajouté en BDD") {
                

                $object = "Inscription réussie";
                $content = "<h1>Félicitations, votre inscription a été réussie.Pour activer le compte cliquer sur le lien ci-dessous :</h1>
                <a href='https://localhost:8000/register/activate/".$user->getId()."'>Activer</a>";
                $destinataire = $user->getEmail();
        
                $this->messagerie->sendMail($object, $content, $destinataire);
        
          
            }

            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'RegisterController',
            'msg' => $msg,
        ]);

        }
        
        #[Route('/register/activate/{id}', name: 'app_activate_user')]
        public function activateUser($id): Response
        {
    
            $id = UtilsService::cleanInput($id);
        
            $user = $this->userRepository->find($id);
        
            if ($user) {
             
                $user->setActivated(true);
                $this->em->flush();
                
            
                return $this->redirectToRoute('app_login');
            } else {
              
                return $this->redirectToRoute('app_register');
            }
        }
    }