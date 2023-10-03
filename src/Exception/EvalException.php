<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Exception;

use RuntimeException;

class EvalException extends RuntimeException
{
	protected readonly string $sourceCode;

	public function getSourceCode(): string
	{
		return $this->sourceCode;
	}

	public function setSourceCode(string $sourceCode): void
	{
		$this->sourceCode = $sourceCode;
	}
}