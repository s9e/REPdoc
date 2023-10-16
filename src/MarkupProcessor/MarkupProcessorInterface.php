<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\MarkupProcessor;

use s9e\REPdoc\EvalImplementation\EvalInterface;

interface MarkupProcessorInterface
{
	/**
	* Return the list of file extensions supported by this processor
	*
	* @return string[]
	*/
	public function getSupportedFileExtensions(): array;

	/**
	* Process all the code blocks in given text, evaluate them, then patch the output into the text
	*/
	public function process(string $text, EvalInterface $eval): string;
}