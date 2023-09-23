<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\MarkupProcessor;

use function preg_replace_callback;
use s9e\REPdoc\EvalImplementation\EvalInterface;

class Markdown implements MarkupProcessorInterface
{
	public function process(string $text, EvalInterface $eval): string
	{
		return preg_replace_callback(
			'(^((```++|~~~++)php\\n(.*?)^\\2\\n(```++|~~~++)\\w*+\\n).*?^\\4(?!\\N))ms',
			fn (array $m) => $m[1] . $eval($m[3]) . "\n" . $m[4],
			$text
		);
	}
}