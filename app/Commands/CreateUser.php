<?php

namespace App\Commands;

use App\Facades\UserFacade;
use Nette\Utils\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUser extends Command
{

	/** @var UserFacade */
	private $userFacade;

	public function __construct(UserFacade $userFacade)
	{
		parent::__construct('app:create-user');
		$this->userFacade = $userFacade;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		/** @var QuestionHelper $questionHelper */
		$questionHelper = $this->getHelper('question');

		$usernameQuestion = new Question('E-mail: ');
		$usernameQuestion->setValidator(function ($value) {
			if (trim($value) === '') {
				throw new \Exception('E-mail can not be empty');
			}
			if (!Validators::isEmail($value)) {
				throw new \Exception('E-mail is not valid');
			}

			return $value;
		});

		$passwordQuestion = new Question('Password: ');

		$passwordQuestion->setValidator(function ($value) {
			if (trim($value) === '') {
				throw new \Exception('The password can not be empty');
			}

			return $value;
		});

		$passwordQuestion->setHidden(TRUE);
		$passwordQuestion->setHiddenFallback(FALSE);

		$username = $questionHelper->ask($input, $output, $usernameQuestion);
		$password = $questionHelper->ask($input, $output, $passwordQuestion);

		$name = $questionHelper->ask($input, $output, new Question('Name: '));

		$user = $this->userFacade->add($username, $password, $name);

		$output->writeln('User ' . $user->getEmail() . ' was successfully created!');
	}
}
