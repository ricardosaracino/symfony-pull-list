<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user instanceof User) {
			return;
		}

        if (!$user->getIsActive()) {
            throw new AccountExpiredException();
        }
	}

	public function checkPostAuth(UserInterface $user)
	{
	}
}