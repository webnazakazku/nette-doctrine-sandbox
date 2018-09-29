<?php

namespace App\Model\User\Commands;

use App;
use Nette;
use Nette\Utils\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class AddUser extends Command
{

	/** @var App\Model\User\Facades\UserFacade */
	private $userFascade;

	public function __construct(App\Model\User\Facades\UserFacade $userFascade)
	{
		parent::__construct();

		$this->userFascade = $userFascade;
	}

	protected function configure()
	{
		$this->setName('app:add-user')
			->setDescription('Add new user');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/* @var $questionHelper QuestionHelper */
		$questionHelper = $this->getHelper('question');

		$userFascade = $this->userFascade;

		$nameQuestion = new Question('Name: ');
		$nameQuestion->setValidator(function ($value) use ($userFascade) {
			if (trim($value) === '') {
				throw new \Exception('Name can not be empty');
			}

			$name = $userFascade->findUserBy(['name' => $value]);

			if ($name) {
				throw new \Exception('Name is already registered');
			}

			return $value;
		});

		$emailQuestion = new Question('E-mail: ');
		$emailQuestion->setValidator(function ($value) use($userFascade) {
			if (trim($value) === '') {
				throw new \Exception('E-mail can not be empty');
			}
			if (!Validators::isEmail($value)) {
				throw new \Exception('E-mail is not valid');
			}

			$email = $userFascade->findUserBy(['email' => $value]);

			if ($email) {
				throw new \Exception('E-mail address is already registered');
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

		$values['name'] = $questionHelper->ask($input, $output, $nameQuestion);
		$values['email'] = $questionHelper->ask($input, $output, $emailQuestion);
		$values['password'] = $questionHelper->ask($input, $output, $passwordQuestion);

		$password = $values['password'];

		$confirmPasswordQuestion = new Question('Confirm password: ');
		$confirmPasswordQuestion->setValidator(function ($value) use ($password) {
			if ($value != $password) {
				throw new \Exception("The password doesn't match");
			}

			return $value;
		});
		$confirmPasswordQuestion->setHidden(TRUE);
		$confirmPasswordQuestion->setHiddenFallback(FALSE);

		$questionHelper->ask($input, $output, $confirmPasswordQuestion);

		$values['recoveryToken'] = null;
		$values = \Nette\Utils\ArrayHash::from($values);

		$user = $this->userFascade->add($values);

		$output->writeln('User ' . $user->getName() . ' was successfully created!');
	}
}
