<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Wallet;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Service\CalculationService;
use App\Service\PasswordGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/clients')]
#[IsGranted('ROLE_ADMIN')]
class ClientManagementController extends AbstractController
{
    #[Route('', name: 'admin_clients')]
    public function index(ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAllClients();

        return $this->render('admin/clients/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'admin_clients_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        PasswordGeneratorService $passwordGenerator
    ): Response {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client, ['is_creation' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Generate temporary password
            $temporaryPassword = $passwordGenerator->generateTemporaryPassword();
            $client->setPassword($passwordHasher->hashPassword($client, $temporaryPassword));

            // Create wallet for new client with initial balance of 500€
            $wallet = new Wallet();
            $wallet->setBalance('500.00');
            $wallet->setClient($client);
            $client->setWallet($wallet);

            $entityManager->persist($client);
            $entityManager->persist($wallet);
            $entityManager->flush();

            // Display temporary password to admin
            $this->addFlash('success', 'Client created successfully.');
            $this->addFlash('info', 'Temporary password: ' . $temporaryPassword);

            return $this->redirectToRoute('admin_clients');
        }

        return $this->render('admin/clients/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_clients_show', requirements: ['id' => '\d+'])]
    public function show(Client $client, CalculationService $calculationService): Response
    {
        $wallet = $client->getWallet();
        $portfolioSummary = null;

        if ($wallet) {
            $portfolioSummary = [
                'balance' => $wallet->getBalance(),
                'portfolioValue' => $calculationService->calculateTotalPortfolioValue($wallet),
                'profitLoss' => $calculationService->calculateTotalProfitLoss($wallet),
                'netWorth' => $calculationService->calculateNetWorth($wallet),
            ];
        }

        return $this->render('admin/clients/show.html.twig', [
            'client' => $client,
            'portfolioSummary' => $portfolioSummary,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_clients_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        Client $client,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Client updated successfully.');

            return $this->redirectToRoute('admin_clients');
        }

        return $this->render('admin/clients/edit.html.twig', [
            'form' => $form,
            'client' => $client,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_clients_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Request $request,
        Client $client,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client deleted successfully.');
        }

        return $this->redirectToRoute('admin_clients');
    }

    #[Route('/{id}/toggle-status', name: 'admin_clients_toggle_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggleStatus(
        Request $request,
        Client $client,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('toggle' . $client->getId(), $request->request->get('_token'))) {
            $client->setIsActive(!$client->isActive());
            $client->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Client status updated successfully.');
        }

        return $this->redirectToRoute('admin_clients');
    }
}
