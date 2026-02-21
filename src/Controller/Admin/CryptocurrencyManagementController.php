<?php

namespace App\Controller\Admin;

use App\Entity\Cryptocurrency;
use App\Form\CryptocurrencyType;
use App\Repository\CryptocurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cryptocurrencies')]
#[IsGranted('ROLE_ADMIN')]
class CryptocurrencyManagementController extends AbstractController
{
    #[Route('', name: 'admin_cryptocurrencies')]
    public function index(CryptocurrencyRepository $cryptoRepository): Response
    {
        return $this->render('admin/cryptocurrencies/index.html.twig', [
            'cryptocurrencies' => $cryptoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_cryptocurrencies_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $crypto = new Cryptocurrency();
        $form = $this->createForm(CryptocurrencyType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crypto);
            $entityManager->flush();

            $this->addFlash('success', 'Cryptocurrency created successfully.');

            return $this->redirectToRoute('admin_cryptocurrencies');
        }

        return $this->render('admin/cryptocurrencies/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_cryptocurrencies_show', requirements: ['id' => '\d+'])]
    public function show(Cryptocurrency $crypto): Response
    {
        return $this->render('admin/cryptocurrencies/show.html.twig', [
            'crypto' => $crypto,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cryptocurrencies_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Cryptocurrency $crypto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CryptocurrencyType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Cryptocurrency updated successfully.');

            return $this->redirectToRoute('admin_cryptocurrencies');
        }

        return $this->render('admin/cryptocurrencies/edit.html.twig', [
            'form' => $form,
            'crypto' => $crypto,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_cryptocurrencies_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Cryptocurrency $crypto, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $crypto->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_cryptocurrencies');
        }

        if ($crypto->getHoldings()->count() > 0 || $crypto->getTransactions()->count() > 0) {
            $this->addFlash('error', 'Cannot delete this cryptocurrency: it has active holdings or transactions.');

            return $this->redirectToRoute('admin_cryptocurrencies');
        }

        $entityManager->remove($crypto);
        $entityManager->flush();

        $this->addFlash('success', 'Cryptocurrency deleted successfully.');

        return $this->redirectToRoute('admin_cryptocurrencies');
    }
}
