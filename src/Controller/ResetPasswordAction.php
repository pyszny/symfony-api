<?php

namespace App\Controller;

use App\Entity\User;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResetPasswordAction
{
    private $validator;
    private $userPasswordEncoder;
    private $entityManager;
    private $tokenManager;

    public function __construct(
            ValidatorInterface $validator, 
            UserPasswordEncoderInterface $userPasswordEncoder,
            EntityManagerInterface $entityManager,
            JWTTokenManagerInterface $tokenManager
    )
    {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(User $data)
    {
        // $reset = new ResetPasswordAction();
        // $reset();
        // var_dump(
        //     $data->getNewPassword(), 
        //     $data->getNewRetypedPassword(), 
        //     $data->getOldPassword(),
        //     $data->getRetypedPassword()
        // );
        // die;
        $this->validator->validate($data);

        $data->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $data, $data->getNewPassword()
            )
        );

        // After password change, old tokens are still valid
        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);

        // Validator will only run after we return the data from this action!
        // Entity is persisted automatically when validation passes
    }
}