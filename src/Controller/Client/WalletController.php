<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Service\TransactionService;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client/wallet')]
#[IsGranted('ROLE_CLIENT')]
class WalletController extends AbstractController
{
    #[Route('', name: 'client_wallet')]
    public function index(WalletService $walletService): Response
    {
        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $portfolioSummary = $walletService->getPortfolioSummary($wallet);
        $holdings = $walletService->getAllHoldings($wallet);

        return $this->render('client/wallet.html.twig', [
            'wallet' => $wallet,
            'portfolioSummary' => $portfolioSummary,
            'holdings' => $holdings,
        ]);
    }

    #[Route('/transactions', name: 'client_transactions')]
    public function transactions(TransactionService $transactionService): Response
    {
        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $transactions = $transactionService->getTransactionsByWallet($wallet);

        return $this->render('client/transactions.html.twig', [
            'transactions' => $transactions,
        ]);
    }
}
