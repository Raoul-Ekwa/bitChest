<?php

namespace App\Controller\Admin;

use App\Repository\CryptocurrencyRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        CryptocurrencyRepository $cryptoRepository,
        TransactionRepository $transactionRepository
    ): Response {
        $totalClients = $userRepository->countClients();
        $cryptocurrencies = $cryptoRepository->findAll();
        $recentTransactions = $transactionRepository->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'totalClients' => $totalClients,
            'cryptocurrencies' => $cryptocurrencies,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    #[Route('/cryptocurrencies', name: 'admin_cryptocurrencies')]
    public function cryptocurrencies(CryptocurrencyRepository $cryptoRepository): Response
    {
        return $this->render('admin/cryptocurrencies/index.html.twig', [
            'cryptocurrencies' => $cryptoRepository->findAll(),
        ]);
    }
}
