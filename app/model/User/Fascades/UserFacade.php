<?php

namespace App\Model\User\Facades;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette;
use App\Model\User\Entities\User;

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
	 * @param Nette\Utils\ArrayHash $values
	 *
	 * @return User
	 */
	public function add($values)
	{
		$user = new User;

		$user->setName($values->name);
		$user->setEmail($values->email);
		$user->setPassword($values->password);

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

	public function findUserBy($criteria)
	{
		return $this->users->findBy($criteria);
	}
}
