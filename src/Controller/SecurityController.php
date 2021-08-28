<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/magic', name: 'magic')]
    public function magic(
        UserRepository $userRepository,
        LoginLinkHandlerInterface $loginLinkHandler,
        MailerInterface $mailer
    ): Response {
        $users = $userRepository->findAll(); 
        foreach ($users as $user) {
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $email = (new Email())
                ->from(new Address('bot@gmail.com', 'Boot'))
                ->to($user->getEmail())
                ->subject('Subject email magic')
                    ->text('You magic email from body is ' . $loginLinkDetails->getUrl()); 
                $mailer->send($email);
                 
        } 
        return new Response('Magic !');
    }
}
