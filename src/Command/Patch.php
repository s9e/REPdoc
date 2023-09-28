<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpProcess;
use function class_exists;
use s9e\REPdoc\EvalImplementation\NativeEval;
use s9e\REPdoc\EvalImplementation\SymfonyProcess;
use s9e\REPdoc\Filesystem;
use s9e\REPdoc\MarkupProcessorRepository;
use s9e\REPdoc\MarkupProcessor\Html;
use s9e\REPdoc\MarkupProcessor\Markdown;
use s9e\REPdoc\Patcher;

#[AsCommand(name: 'repdoc:patch', description: 'Patches target files and directories')]
class Patch extends Command
{
	protected bool $hasSymfonyProcess;

	protected function configure(): void
	{
		$this->hasSymfonyProcess = class_exists(PhpProcess::class);

		$this->addOption(
			'process-isolation',
			null,
			InputOption::VALUE_NEGATABLE,
			'Whether to execute the PHP code in its own process (requires symfony/process)',
			false
		);

		$this->addOption(
			'recursive',
			null,
			InputOption::VALUE_NEGATABLE,
			'Whether to recurse into directories',
			true
		);

		$this->addArgument('targets', InputArgument::IS_ARRAY);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io               = new SymfonyStyle($input, $output);
		$recursive        = (bool) $input->getOption('recursive');
		$targets          = (array) $input->getArgument('targets');
		$processIsolation = (bool) $input->getOption('process-isolation');

		if (empty($targets))
		{
			$io->error('No targets provided');

			return Command::FAILURE;
		}
		if ($processIsolation && !$this->hasSymfonyProcess)
		{
			$io->error('Cannot use process isolation, make sure symfony/process is installed');

			return Command::FAILURE;
		}

		$eval       = $processIsolation ? new SymfonyProcess : new NativeEval;
		$filesystem = new Filesystem;
		$repository = new MarkupProcessorRepository([new Html, new Markdown]);
		$patcher    = new Patcher(
			evalImplementation:  $eval,
			filesystem:          $filesystem,
			processorRepository: $repository
		);

		$extensions = $repository->getSupportedFileExtensions();
		$paths      = [];
		foreach ($targets as $target)
		{
			foreach ($filesystem->getFilepaths($target, $extensions, $recursive) as $path)
			{
				$paths[] = $path;
			}
		}

		$changed = [];
		foreach ($io->progressIterate($paths) as $path)
		{
			if ($patcher->patchFile($path))
			{
				$changed[] = $path;
			}
		}

		return Command::SUCCESS;
	}
}