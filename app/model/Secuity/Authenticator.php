<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\User\Fascades\UserFascade;
use Nette\Security\Passwords;
use Nette\Security as NS;

class Authenticator implements NS\IAuthenticator
{

	/**
	 * @var App\Model\User\Fascades\UserFascade
	 */
	private $userFascade;

	/**
	 * @param array $credentials
	 *
	 * @return NS\Identity
	 * @throws NS\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		$user = $this->userFascade->findOneBy(['email' => $email]);

		if (!$user) {
			throw new AuthenticationException('Invalid credentials.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user->getPassword())) {
			throw new AuthenticationException('Invalid credentials.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($user->getPassword())) {
			$user->setPassword($password);
			$this->em->flush();
		}

		return new Nette\Security\Identity($user->getId(), NULL, ['name' => $user->getName(), 'email' => $user->getEmail()]);
	}
}
