<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

use Throwable;
use function ob_get_clean, ob_start;
use s9e\REPdoc\Exception\EvalException;

class NativeEval implements EvalInterface
{
	public function __invoke(string $code): string
	{
		try
		{
			ob_start();
			eval($code);
		}
		catch (Throwable $previous)
		{
			$evalException = new EvalException(previous: $previous);
			$evalException->setSourceCode($code);

			throw $evalException;
		}
		finally
		{
			$output = ob_get_clean();
		}

		return $output;
	}
}