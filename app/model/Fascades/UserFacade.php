<?php

namespace App\Facades;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use App\Model\Entities\User;

class UserFacade
{

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
	 * @param $email
	 * @param $password
	 * @param string $name
	 *
	 * @return User
	 */
	public function add($email, $password, $name = 'Anonymous')
	{
		$user = new User;
		$user->setEmail($email);
		$user->setPassword($password);
		$user->setName($name);

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}
}
