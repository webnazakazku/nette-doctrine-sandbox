<?php

namespace App\Model\Security;

use Nette;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use App\Model\Entities\User;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Passwords;

/**
 * Class Authenticator
 *
 * @package App\Model\Security
 */
class Authenticator implements IAuthenticator
{

	use Nette\SmartObject;

	/** @var EntityManager */
	private $em;

	/** @var EntityRepository */
	private $users;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->users = $em->getRepository(User::class);
	}

	/**
	 * @param array $credentials
	 * @return null|User
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		$user = $this->users->findOneBy(['email' => $email]);

		if (!$user) {
			throw new AuthenticationException('Invalid credentials.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user->getPassword())) {
			throw new AuthenticationException('Invalid credentials.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($user->getPassword())) {
			$user->setPassword($password);
			$this->em->flush();
		}

		return $user;
	}
}
