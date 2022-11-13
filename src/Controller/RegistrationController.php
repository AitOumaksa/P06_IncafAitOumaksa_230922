<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\JWTService;
use App\Service\SendMailService;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatar')->getData();
            $path = 'assets/img/userAvatar/';
            $fichier = md5(uniqid()) . '.' . $avatarFile->guessExtension();
            $avatarFile->move($this->getParameter('users_directory'), $fichier);
            $user->setAvatar($path . $fichier);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
             $mail->sendEmail(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur le site snowTricks',
                'register',
                compact('user', 'token')
            );
            
            $this->addFlash('warning', 'Vous devez activer votre compte via le lien envoyé dans votre boite E-mail');
            return $this->redirectToRoute('register');

            
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $usersRepository, EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $payload = $jwt->getPayload($token);

            $user = $usersRepository->find($payload['user_id']);

            if($user && !$user->isVerified()){
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('home');
            }
        }

        $this->addFlash('danger', 'Le token est invalide');
        return $this->redirectToRoute('login');
    }
}
