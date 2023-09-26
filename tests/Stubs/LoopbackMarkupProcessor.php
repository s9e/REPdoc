<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Tests\Stubs;

use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

class LoopbackMarkupProcessor implements MarkupProcessorInterface
{
	public function __construct(public array $extensions)
	{
	}

	public function getSupportedFileExtensions(): array
	{
		return $this->extensions;
	}

	public function process(string $text, EvalInterface $eval): string
	{
		return $text;
	}
}