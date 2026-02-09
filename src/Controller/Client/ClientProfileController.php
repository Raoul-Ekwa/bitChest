<?php

namespace App\Controller\Client;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client/profile')]
#[IsGranted('ROLE_CLIENT')]
class ClientProfileController extends AbstractController
{
    #[Route('', name: 'client_profile')]
    public function index(): Response
    {
        return $this->render('client/profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'client_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');

            return $this->redirectToRoute('client_profile');
        }

        return $this->render('client/profile/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
