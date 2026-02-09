<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Wallet;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $client = new Client();
        $form = $this->createForm(RegistrationType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setPassword(
                $passwordHasher->hashPassword(
                    $client,
                    $form->get('plainPassword')->getData()
                )
            );

            // Create wallet for new client
            $wallet = new Wallet();
            $wallet->setClient($client);
            $client->setWallet($wallet);

            $entityManager->persist($client);
            $entityManager->persist($wallet);
            $entityManager->flush();

            $this->addFlash('success', 'Your account has been created successfully. You can now log in.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
