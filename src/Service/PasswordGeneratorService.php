<?php

namespace App\Service;

class PasswordGeneratorService
{
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const NUMBERS = '0123456789';
    private const SPECIAL = '!@#$%^&*';

    public function generateTemporaryPassword(int $length = 12): string
    {
        $password = '';

        // Ensure at least one of each required character type
        $password .= self::UPPERCASE[random_int(0, strlen(self::UPPERCASE) - 1)];
        $password .= self::LOWERCASE[random_int(0, strlen(self::LOWERCASE) - 1)];
        $password .= self::NUMBERS[random_int(0, strlen(self::NUMBERS) - 1)];
        $password .= self::SPECIAL[random_int(0, strlen(self::SPECIAL) - 1)];

        // Fill the rest with random characters from all sets
        $allChars = self::UPPERCASE . self::LOWERCASE . self::NUMBERS . self::SPECIAL;
        $allCharsLength = strlen($allChars);

        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, $allCharsLength - 1)];
        }

        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }
}
