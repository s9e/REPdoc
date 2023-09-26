<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

use RuntimeException;
use Symfony\Component\Process\PhpProcess;
use function class_exists;

class SymfonyProcess implements EvalInterface
{
	public function __invoke(string $code): string
	{
		if (!class_exists(PhpProcess::class))
		{
			throw new RuntimeException('Cannot find ' . PhpProcess::class . ', did you install symfony/process?');
		}

		$process = new PhpProcess('<?php ' . $code);
		$process->mustRun();

		return $process->getOutput();
	}
}