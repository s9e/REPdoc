<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

use function ob_get_clean, ob_start;

class NativeEval implements EvalInterface
{
	public function __invoke(string $code): string
	{
		ob_start();
		eval($code);

		return ob_get_clean();
	}
}