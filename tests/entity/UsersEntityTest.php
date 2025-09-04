<?php

namespace App\Entity\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersEntityTest extends KernelTestCase
{

    private const EMAIL_CONSTRAINT_MESSAGE = 'L\'email renseigné n\'est pas un email valide';
    private const NOT_BLANK_CONSTRAINT_MESSAGE = 'Ce champ ne peut pas être vide.';
    private const INVALID_EMAIL_VALUE = 'coco@coco';
    private const PASSWORD_REGEX_CONSTRAINT_MESSAGE = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
    private const NICKNAME_LENGTH_CONSTRAINT_MESSAGE = 'Minimum 3 caractères';
    private const SHORT_NICKNAME_VALUE = 'ab';
    private const LONG_NICKNAME_VALUE = 'abcdefghijklmnopqrstuvwxyz';
    private const VALID_NICKNAME_VALUE = 'coco';
    private const VALID_EMAIL_VALUE = 'coco@coco.com';
    private const VALID_PASSWORD_VALUE = 'Coco1234';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get('validator');
    }
    public function testValidEntity(): void
    {
        $user = new \App\Entity\Users();
        $user->setNickname(self::VALID_NICKNAME_VALUE);
        $user->setEmail(self::VALID_EMAIL_VALUE);
        $user->setPassword(self::VALID_PASSWORD_VALUE);

        $errors = $this->validator->validate($user);
        $this->assertCount(0, $errors);
    }
}
