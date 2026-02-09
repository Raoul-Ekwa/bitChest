<?php

namespace App\DataFixtures;

use App\Entity\Administrator;
use App\Entity\Client;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Create Administrator
        $admin = new Administrator();
        $admin->setEmail('admin@bitchest.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('BitChest');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($admin);

        // Create test clients
        $clients = [
            [
                'email' => 'jean.dupont@email.com',
                'firstName' => 'Jean',
                'lastName' => 'Dupont',
                'phone' => '0612345678',
                'address' => '123 Rue de Paris, 75001 Paris',
                'balance' => '500.00',
            ],
            [
                'email' => 'marie.martin@email.com',
                'firstName' => 'Marie',
                'lastName' => 'Martin',
                'phone' => '0623456789',
                'address' => '45 Avenue des Champs, 69001 Lyon',
                'balance' => '1500.00',
            ],
            [
                'email' => 'pierre.durand@email.com',
                'firstName' => 'Pierre',
                'lastName' => 'Durand',
                'phone' => '0634567890',
                'address' => '78 Boulevard Victor Hugo, 33000 Bordeaux',
                'balance' => '2500.00',
            ],
            [
                'email' => 'sophie.bernard@email.com',
                'firstName' => 'Sophie',
                'lastName' => 'Bernard',
                'phone' => '0645678901',
                'address' => '12 Rue du Commerce, 31000 Toulouse',
                'balance' => '750.00',
            ],
            [
                'email' => 'thomas.petit@email.com',
                'firstName' => 'Thomas',
                'lastName' => 'Petit',
                'phone' => '0656789012',
                'address' => '89 Rue de la République, 13001 Marseille',
                'balance' => '3000.00',
            ],
        ];

        foreach ($clients as $data) {
            $client = new Client();
            $client->setEmail($data['email']);
            $client->setFirstName($data['firstName']);
            $client->setLastName($data['lastName']);
            $client->setPhone($data['phone']);
            $client->setAddress($data['address']);
            $client->setPassword($this->passwordHasher->hashPassword($client, 'password123'));

            // Create wallet for client
            $wallet = new Wallet();
            $wallet->setBalance($data['balance']);
            $wallet->setClient($client);
            $client->setWallet($wallet);

            $manager->persist($client);
            $manager->persist($wallet);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CryptocurrencyFixtures::class,
        ];
    }
}
