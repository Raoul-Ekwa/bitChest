<?php

namespace App\Tests\Functional\Controller\Admin;

use App\Entity\Administrator;
use App\Entity\Cryptocurrency;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CryptocurrencyManagementControllerTest extends WebTestCase
{
    private const ADMIN_EMAIL = 'test_crypto_admin@bitchest.test';

    // -------------------------------------------------------------------------
    // Lifecycle: create test admin once per class, clean up after
    // -------------------------------------------------------------------------

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::bootKernel();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Remove leftover from a previous failed run
        $existing = $em->getRepository(Administrator::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }

        $admin = new Administrator();
        $admin->setEmail(self::ADMIN_EMAIL);
        $admin->setFirstName('Test');
        $admin->setLastName('Admin');

        $hasher = $container->get(UserPasswordHasherInterface::class);
        $admin->setPassword($hasher->hashPassword($admin, 'test123'));

        $em->persist($admin);
        $em->flush();

        static::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        static::bootKernel();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $admin = $em->getRepository(Administrator::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        if ($admin) {
            $em->remove($admin);
            $em->flush();
        }

        static::ensureKernelShutdown();
        parent::tearDownAfterClass();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getAdmin(): Administrator
    {
        return static::getContainer()
            ->get(EntityManagerInterface::class)
            ->getRepository(Administrator::class)
            ->findOneBy(['email' => self::ADMIN_EMAIL]);
    }

    private function createTestCrypto(string $symbol): Cryptocurrency
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $crypto = new Cryptocurrency();
        $crypto->setSymbol($symbol);
        $crypto->setName('TestCoin ' . $symbol);
        $crypto->setCurrentPrice('100.00000000');
        $em->persist($crypto);
        $em->flush();

        return $crypto;
    }

    private function removeTestCrypto(int $id): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $crypto = $em->find(Cryptocurrency::class, $id);
        if ($crypto) {
            $em->remove($crypto);
            $em->flush();
        }
    }

    // -------------------------------------------------------------------------
    // Unauthenticated access — all admin routes redirect to /login
    // -------------------------------------------------------------------------

    public function testIndexRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/cryptocurrencies');
        $this->assertResponseRedirects('/login');
    }

    public function testNewPageRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/cryptocurrencies/new');
        $this->assertResponseRedirects('/login');
    }

    public function testShowPageRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/cryptocurrencies/999');
        $this->assertResponseRedirects('/login');
    }

    public function testEditPageRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/cryptocurrencies/999/edit');
        $this->assertResponseRedirects('/login');
    }

    public function testDeleteRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();
        $client->request('POST', '/admin/cryptocurrencies/999/delete');
        $this->assertResponseRedirects('/login');
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function testIndexIsAccessibleForAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin());
        $client->request('GET', '/admin/cryptocurrencies');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
    }

    // -------------------------------------------------------------------------
    // New / Create
    // -------------------------------------------------------------------------

    public function testNewFormIsAccessibleForAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin());
        $client->request('GET', '/admin/cryptocurrencies/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('[name="cryptocurrency[symbol]"]');
        $this->assertSelectorExists('[name="cryptocurrency[currentPrice]"]');
    }

    public function testCreateValidCryptocurrencyRedirects(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin());

        $client->request('GET', '/admin/cryptocurrencies/new');
        $client->submitForm('Create Cryptocurrency', [
            'cryptocurrency[symbol]' => 'CRUD',
            'cryptocurrency[name]'   => 'CrudCoin',
            'cryptocurrency[currentPrice]' => '12345.50',
            'cryptocurrency[image]'  => '',
        ]);

        $this->assertResponseRedirects('/admin/cryptocurrencies');

        // Clean up
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $crypto = $em->getRepository(Cryptocurrency::class)->findOneBy(['symbol' => 'CRUD']);
        if ($crypto) {
            $em->remove($crypto);
            $em->flush();
        }
    }

    public function testCreateWithBlankFieldsShowsFormErrors(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin());

        $client->request('GET', '/admin/cryptocurrencies/new');
        $client->submitForm('Create Cryptocurrency', [
            'cryptocurrency[symbol]' => '',
            'cryptocurrency[name]'   => '',
            'cryptocurrency[currentPrice]' => '',
        ]);

        // Symfony 7 returns 422 Unprocessable Content when a rendered form has errors
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('form');
    }

    public function testCreateWithInvalidPriceShowsFormError(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin());

        $client->request('GET', '/admin/cryptocurrencies/new');
        $client->submitForm('Create Cryptocurrency', [
            'cryptocurrency[symbol]' => 'TMP',
            'cryptocurrency[name]'   => 'TmpCoin',
            'cryptocurrency[currentPrice]' => 'not-a-number',
        ]);

        // Symfony 7 returns 422 Unprocessable Content when a rendered form has errors
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('form');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function testShowIsAccessibleForAdmin(): void
    {
        $client = static::createClient();
        $crypto = $this->createTestCrypto('SHOW');
        $id = $crypto->getId();

        $client->loginUser($this->getAdmin());
        $client->request('GET', '/admin/cryptocurrencies/' . $id);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'SHOW');

        $this->removeTestCrypto($id);
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function testEditFormIsPreFilledWithCurrentValues(): void
    {
        $client = static::createClient();
        $crypto = $this->createTestCrypto('EDIT');
        $id = $crypto->getId();

        $client->loginUser($this->getAdmin());
        $client->request('GET', '/admin/cryptocurrencies/' . $id . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertInputValueSame('cryptocurrency[name]', 'TestCoin EDIT');

        $this->removeTestCrypto($id);
    }

    public function testEditValidCryptocurrencyRedirectsAndPersists(): void
    {
        $client = static::createClient();
        $crypto = $this->createTestCrypto('UPD');
        $id = $crypto->getId();

        $client->loginUser($this->getAdmin());
        $client->request('GET', '/admin/cryptocurrencies/' . $id . '/edit');
        $client->submitForm('Update', [
            'cryptocurrency[symbol]'       => 'UPD',
            'cryptocurrency[name]'         => 'UpdatedCoin',
            'cryptocurrency[currentPrice]' => '200.00',
        ]);

        $this->assertResponseRedirects('/admin/cryptocurrencies');

        // Verify the update was persisted
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $updated = $em->find(Cryptocurrency::class, $id);
        $this->assertSame('UpdatedCoin', $updated->getName());

        $this->removeTestCrypto($id);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function testDeleteWithValidCsrfRemovesCrypto(): void
    {
        $client = static::createClient();
        $crypto = $this->createTestCrypto('DEL');
        $id = $crypto->getId();

        $client->loginUser($this->getAdmin());

        // GET the index page to obtain a valid CSRF token from the modal form
        $crawler = $client->request('GET', '/admin/cryptocurrencies');
        $form = $crawler->filter('#deleteModal' . $id . ' form')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/cryptocurrencies');

        // Verify deletion
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $this->assertNull($em->find(Cryptocurrency::class, $id));
    }

    public function testDeleteWithInvalidCsrfDoesNotDelete(): void
    {
        $client = static::createClient();
        $crypto = $this->createTestCrypto('NDEL');
        $id = $crypto->getId();

        $client->loginUser($this->getAdmin());
        $client->request('POST', '/admin/cryptocurrencies/' . $id . '/delete', [
            '_token' => 'invalid_csrf_token',
        ]);

        $this->assertResponseRedirects('/admin/cryptocurrencies');

        // Verify crypto was NOT deleted
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $this->assertNotNull($em->find(Cryptocurrency::class, $id));

        $this->removeTestCrypto($id);
    }
}
