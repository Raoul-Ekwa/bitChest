<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Repository\CryptocurrencyRepository;
use App\Service\CryptocurrencyService;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class ClientDashboardController extends AbstractController
{
    #[Route('', name: 'client_dashboard')]
    public function index(
        WalletService $walletService,
        CryptocurrencyRepository $cryptoRepository
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $portfolioSummary = $walletService->getPortfolioSummary($wallet);
        $cryptocurrencies = $cryptoRepository->findAll();

        return $this->render('client/dashboard.html.twig', [
            'client' => $client,
            'wallet' => $wallet,
            'portfolioSummary' => $portfolioSummary,
            'cryptocurrencies' => $cryptocurrencies,
        ]);
    }

    #[Route('/cryptocurrencies', name: 'client_cryptocurrencies')]
    public function cryptocurrencies(CryptocurrencyRepository $cryptoRepository): Response
    {
        return $this->render('client/cryptocurrencies/index.html.twig', [
            'cryptocurrencies' => $cryptoRepository->findAll(),
        ]);
    }

    #[Route('/cryptocurrencies/{id}', name: 'client_cryptocurrency_show', requirements: ['id' => '\d+'])]
    public function showCryptocurrency(
        int $id,
        CryptocurrencyService $cryptoService
    ): Response {
        $crypto = $cryptoService->getCryptocurrency($id);

        if (!$crypto) {
            throw $this->createNotFoundException('Cryptomonnaie non trouvée.');
        }

        $priceHistory = $cryptoService->getPriceHistory($crypto, 30);
        $priceChange = $cryptoService->getPriceChange24h($crypto);

        /** @var Client $client */
        $client = $this->getUser();
        $wallet = $client->getWallet();

        $holding = null;
        foreach ($wallet->getHoldings() as $h) {
            if ($h->getCryptocurrency()->getId() === $crypto->getId()) {
                $holding = $h;
                break;
            }
        }

        return $this->render('client/cryptocurrencies/show.html.twig', [
            'crypto' => $crypto,
            'priceHistory' => $priceHistory,
            'priceChange' => $priceChange,
            'holding' => $holding,
        ]);
    }
}
