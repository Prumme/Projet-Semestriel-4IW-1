<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;
class ContactUsDTO
{
    #[Assert\NotBlank(message: 'Please enter your email')]
    public string $email;
    #[Assert\NotBlank(message: 'Please enter your first name')]
    public string $firstname;
    #[Assert\NotBlank(message: 'Please enter your last name')]
    public string $lastname;
    #[Assert\NotBlank(message: 'Please enter your message')]
    public string $content;
}