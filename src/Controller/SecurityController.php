<?php

namespace App\Controller;

use App\Entity\Administrator;
use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute($this->getRedirectRoute());
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute($this->getRedirectRoute());
    }

    private function getRedirectRoute(): string
    {
        $user = $this->getUser();

        if ($user instanceof Administrator) {
            return 'admin_dashboard';
        }

        if ($user instanceof Client) {
            return 'client_dashboard';
        }

        return 'app_login';
    }
}
