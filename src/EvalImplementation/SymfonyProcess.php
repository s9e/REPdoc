<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\EvalImplementation;

use RuntimeException;
use Symfony\Component\Process\PhpProcess;
use const DIRECTORY_SEPARATOR;
use function file_exists, get_included_files, realpath, str_ends_with, var_export;

class SymfonyProcess implements EvalInterface
{
	public function __invoke(string $code): string
	{
		// Try to grab Composer's autoload.php from the list of included files
		$suffix = DIRECTORY_SEPARATOR . 'autoload.php';
		foreach (get_included_files() as $filepath)
		{
			if (str_ends_with($filepath, $suffix))
			{
				$code = 'include ' . var_export($filepath, true) . ";\n" . $code;

				break;
			}
		}

		$process = new PhpProcess("<?php\n" . $code);
		$process->mustRun();

		return $process->getOutput();
	}
}