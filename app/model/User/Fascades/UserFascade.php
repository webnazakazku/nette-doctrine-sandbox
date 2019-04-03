<?php
declare(strict_types=1);

namespace App\Model\User\Fascades;

use App;
use App\Model\EntityManagerDecorator;
use App\Model\User\Entities\User;
use Nette;

class UserFascade
{

	private $em;
	private $userRepository;

	const USERS_REPOSITORY = 'userRepository';

	public function __construct(EntityManagerDecorator $entityManager)
	{
		$this->em = $entityManager;
		$this->userRepository = $entityManager->getRepository(App\Model\User\Entities\User::class);
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
		return $this->userRepository->findBy($criteria);
	}
}
