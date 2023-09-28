<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\MarkupProcessor;

use function preg_replace_callback;
use s9e\REPdoc\EvalImplementation\EvalInterface;

class Html implements MarkupProcessorInterface
{
	public function getSupportedFileExtensions(): array
	{
		return ['html' => 0];
	}

	public function process(string $text, EvalInterface $eval): string
	{
		return preg_replace_callback(
			'(^\\s*<pre[^>]*>\\s*<code\\s+class=["\']?language-php[^>]*>(.*?)</code>\\s*</pre>\\s*<pre[^>]*>\\s*<code[^>]*>\\K.*?(\\n?[ \\t]*</code>\\s*</pre>))ms',
			fn (array $m) => rtrim($eval($m[1]), "\n") . $m[2],
			$text
		);
	}
}