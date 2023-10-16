<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

interface EvalInterface
{
	/**
	* Evaluate given PHP code and return its output
	*/
	public function __invoke(string $code): string;
}