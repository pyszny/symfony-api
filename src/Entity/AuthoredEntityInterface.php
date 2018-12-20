<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 20.12.18
 * Time: 21:11
 */

namespace App\Entity;


use Symfony\Component\Security\Core\User\UserInterface;

interface AuthoredEntityInterface
{
    public function setAuthor(UserInterface $user): AuthoredEntityInterface;
}