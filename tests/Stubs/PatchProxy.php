<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Tests\Stubs;

use s9e\REPdoc\Command\Patch;

class PatchProxy extends Patch
{
	public function setSymfonyProcess(bool $hasSymfonyProcess): void
	{
		$this->hasSymfonyProcess = $hasSymfonyProcess;
	}
}