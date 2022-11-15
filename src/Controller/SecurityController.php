<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forget-password', name: 'forget.password')]
    public function forgetPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        $manager = $doctrine->getManager();
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $manager->persist($user);
                $manager->flush();

                $url = $this->generateUrl('reset.password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $context = ['user' => $user, 'url' => $url];
                $mail->sendEmail(
                    'no-reply@monsite.net',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'reset-password',
                    $context
                );
                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('login');
            }
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('login');
        }
        return $this->render('security/reset-password-request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset.password')]
    public function resetPassword(string $token, Request $request, UserRepository $userRepository, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response 
    {
        $manager = $doctrine->getManager();
        $user = $userRepository->findOneByResetToken($token);
        if($user)
        {
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('login');
            }
            return $this->render('security/reset-password.html.twig',[
                'formPassword' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('login');

    }
}
