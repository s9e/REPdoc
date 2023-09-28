<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpProcess;
use function array_merge, class_exists, count, implode;
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
		$io->writeln('Supported file extensions: ' . implode(', ', $extensions), OutputInterface::VERBOSITY_VERBOSE);

		$paths = [];
		foreach ($targets as $target)
		{
			$io->writeln('Looking for supported files in ' . $target);
			$targetFilepaths = $filesystem->getFilepaths($target, $extensions, $recursive);
			$io->writeln('Files found: ' . count($targetFilepaths));
			$paths = array_merge($paths, $targetFilepaths);

			if ($io->isVerbose())
			{
				foreach ($targetFilepaths as $path)
				{
					$io->writeln('Found ' . $path, OutputInterface::VERBOSITY_VERBOSE);
				}
			}
		}

		$infoSection = $output->section();
		$infoSection->setMaxHeight(1);

		$progressSection = $output->section();
		$progressBar     = new ProgressBar($progressSection);
		$progressBar->start(count($paths));

		$changed = [];
		foreach ($paths as $path)
		{
			$infoSection->writeln('Patching ' . $path);
			if ($patcher->patchFile($path))
			{
				$changed[] = $path;
				$infoSection->writeln('Updated ' . $path, OutputInterface::VERBOSITY_VERBOSE);
			}
			else
			{
				$infoSection->writeln('Skipped ' . $path, OutputInterface::VERBOSITY_VERBOSE);
			}
			$progressBar->advance();
		}

		$io->success('Files changed: ' . count($changed) . ' / ' . count($paths));

		return Command::SUCCESS;
	}
}