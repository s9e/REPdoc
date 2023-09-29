<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\MarkupProcessor;

use function preg_replace_callback, rtrim;
use s9e\REPdoc\EvalImplementation\EvalInterface;

class Markdown implements MarkupProcessorInterface
{
	public function getSupportedFileExtensions(): array
	{
		return ['md'];
	}

	public function process(string $text, EvalInterface $eval): string
	{
		return preg_replace_callback(
			'(^(```+|~~~+)php\\n((?:(?!\\1)\\N*\\n)*+)\\1\\n((?1))\\w*\\n\\K.*?^\\3(?!\\N))ms',
			fn (array $m) => rtrim($eval($m[2]), "\n") . "\n" . $m[3],
			$text
		);
	}
}