<?php

namespace App\Controller\Admin;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/profile')]
#[IsGranted('ROLE_ADMIN')]
class AdminProfileController extends AbstractController
{
    #[Route('', name: 'admin_profile')]
    public function index(): Response
    {
        return $this->render('admin/profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'admin_profile_edit')]
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

            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
