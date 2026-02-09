<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Entity\Cryptocurrency;
use App\Form\BuyTransactionType;
use App\Form\SellTransactionType;
use App\Service\CryptocurrencyService;
use App\Service\TransactionService;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class TransactionController extends AbstractController
{
    #[Route('/buy/{id}', name: 'client_buy', requirements: ['id' => '\d+'])]
    public function buy(
        Request $request,
        Cryptocurrency $crypto,
        TransactionService $transactionService,
        WalletService $walletService
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $form = $this->createForm(BuyTransactionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();

            try {
                $transaction = $transactionService->buy($wallet, $crypto, $quantity);

                $this->addFlash('success', sprintf(
                    'Purchase of %s %s completed for %s EUR.',
                    $quantity,
                    $crypto->getSymbol(),
                    $transaction->getTotalAmount()
                ));

                return $this->redirectToRoute('client_wallet');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $portfolioSummary = $walletService->getPortfolioSummary($wallet);

        return $this->render('client/buy.html.twig', [
            'form' => $form,
            'crypto' => $crypto,
            'wallet' => $wallet,
            'portfolioSummary' => $portfolioSummary,
        ]);
    }

    #[Route('/sell/{id}', name: 'client_sell', requirements: ['id' => '\d+'])]
    public function sell(
        Request $request,
        Cryptocurrency $crypto,
        TransactionService $transactionService,
        WalletService $walletService
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $holding = $walletService->getHolding($wallet, $crypto);

        if (!$holding || bccomp($holding->getQuantity(), '0', 8) === 0) {
            $this->addFlash('error', 'You do not own any ' . $crypto->getName() . '.');
            return $this->redirectToRoute('client_wallet');
        }

        $form = $this->createForm(SellTransactionType::class, null, [
            'max_quantity' => $holding->getQuantity(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();

            try {
                $transaction = $transactionService->sell($wallet, $crypto, $quantity);

                $this->addFlash('success', sprintf(
                    'Sale of %s %s completed for %s EUR.',
                    $quantity,
                    $crypto->getSymbol(),
                    $transaction->getTotalAmount()
                ));

                return $this->redirectToRoute('client_wallet');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('client/sell.html.twig', [
            'form' => $form,
            'crypto' => $crypto,
            'holding' => $holding,
            'wallet' => $wallet,
        ]);
    }
}
