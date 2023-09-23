<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\MarkupProcessor;

use function eval, ob_get_clean, ob_start;
use s9e\REPdoc\Eval\EvalInterface;

interface MarkupProcessorInterface
{
	/**
	* Process all the code blocks in given text, evaluate them, then patch the output
	*
	* @param  string $text Input text
	* @param  Eval   $eval
	* @return void
	*/
	public function process(string $text, EvalInterface $eval): string
	{
	}
}