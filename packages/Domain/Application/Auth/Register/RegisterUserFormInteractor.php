<?php

namespace packages\Domain\Application\Auth\Register;

use packages\Domain\Domain\Common\Prefecture;
use packages\Domain\Domain\User\Gender;
use packages\UseCase\Auth\Register\RegisterUserFormResponse;
use packages\UseCase\Auth\Register\RegisterUserFormUseCaseInterface;

class RegisterUserFormInteractor implements RegisterUserFormUseCaseInterface
{
    /**
     * @return RegisterUserFormResponse
     */
    public function handle(): RegisterUserFormResponse
    {
        /** @var array $prefectures */
        $prefectures = Prefecture::Enum();

        /** @var array $genders */
        $genders = Gender::Enum();

        return new RegisterUserFormResponse($prefectures, $genders);
    }
}
