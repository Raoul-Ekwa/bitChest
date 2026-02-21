<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Transaction;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TransactionVoter extends Voter
{
    public const VIEW = 'TRANSACTION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW
            && $subject instanceof Transaction;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Client) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = $subject;

        return $transaction->getWallet()?->getClient() === $user;
    }
}
