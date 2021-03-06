<?php

namespace App\Presenters;

use App\Forms;
use Nette\Application\UI\Form;

final class SignPresenter extends BasePresenter
{

	/** @persistent */
	public $backlink = '';

	/** @var Forms\SignInFormFactory */
	private $signInFactory;

	public function __construct(Forms\SignInFormFactory $signInFactory)
	{
		$this->signInFactory = $signInFactory;
	}

	/**
	 * Sign-in form factory.
	 * @return Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->create(function () {
				$this->restoreRequest($this->backlink);
				$this->redirect('Homepage:');
			});
	}

	public function actionLogout()
	{
		$this->getUser()->logout();
		$this->redirect('Sign:in');
	}
}
