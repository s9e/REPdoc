<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

use RuntimeException;
use Symfony\Component\Process\PhpProcess;

class SymfonyProcess implements EvalInterface
{
	public function __invoke(string $code): string
	{
		// Try to locate autoload.php so we can include it before any code
		$path = $this->getAutoloadPath();
		if ($path)
		{
			$code = 'include ' . var_export($path, true) . ";\n" . $code;
		}


		$process = new PhpProcess("<?php\n" . $code);
		$process->mustRun();

		return $process->getOutput();
	}

	protected function getAutoloadPath(): ?string
	{
		$paths = [
			__DIR__ . '/../../vendor/autoload.php',
			__DIR__ . '/../../autoload.php'
		];
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				return realpath($path);
			}
		}

		return null;
	}
}