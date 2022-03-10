<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
         if ($this->getUser()) {
             return $this->redirectToRoute('profile', ['username' => $this->getUser()->getUsername()]);
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void {}

    #[Route(path: '/profile/{username}', name: 'profile')]
    public function profile(User $user): Response {
        if ($this->getUser()) {
            return $this->render('security/profile.html.twig', [
                'user' => $user,
            ]);
        }
        else {
            return $this->redirectToRoute('login');
        }
    }
}
