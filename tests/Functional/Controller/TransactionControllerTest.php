<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    // --- Access control ---

    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testClientDashboardRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/client/dashboard');

        $this->assertResponseRedirects('/login');
    }

    public function testAdminDashboardRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/dashboard');

        $this->assertResponseRedirects('/login');
    }

    public function testAdminClientsPageRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/clients');

        $this->assertResponseRedirects('/login');
    }

    public function testClientWalletRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/client/wallet');

        $this->assertResponseRedirects('/login');
    }

    public function testClientTransactionsRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/client/transactions');

        $this->assertResponseRedirects('/login');
    }

    public function testClientCryptocurrenciesRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/client/cryptocurrencies');

        $this->assertResponseRedirects('/login');
    }
}
