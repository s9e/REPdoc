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
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpProcess;
use function array_merge, class_exists, count, get_class, implode;
use s9e\REPdoc\EvalImplementation\EvalInterface;
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
	protected EvalInterface $eval;
	protected Filesystem $filesystem;
	protected MarkupProcessorRepository $repository;
	protected Patcher $patcher;
	protected SymfonyStyle $io;
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
		$this->addArgument('targets', InputArgument::IS_ARRAY | InputArgument::REQUIRED);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->io         = new SymfonyStyle($input, $output);
		$recursive        = (bool) $input->getOption('recursive');
		$targets          = (array) $input->getArgument('targets');
		$processIsolation = (bool) $input->getOption('process-isolation');

		if ($processIsolation && !$this->hasSymfonyProcess)
		{
			$this->io->error('Cannot use process isolation, make sure symfony/process is installed');

			return Command::FAILURE;
		}

		$this->eval       = $processIsolation ? new SymfonyProcess : new NativeEval;
		$this->filesystem = new Filesystem;
		$this->repository = $this->getMarkupProcessorRepository();
		$this->patcher    = new Patcher(
			evalImplementation:  $this->eval,
			filesystem:          $this->filesystem,
			processorRepository: $this->repository
		);

		// Gather/display the list of supported files
		$extensions = $this->repository->getSupportedFileExtensions();
		if ($this->io->isVerbose())
		{
			$this->printSupportedFileExtensions($extensions);
		}

		// Collect the list of supported files among targets
		$paths = [];
		foreach ($targets as $target)
		{
			$this->io->writeln('Looking for supported files in ' . $target, OutputInterface::VERBOSITY_VERBOSE);
			$targetFilepaths = $this->filesystem->getFilepaths($target, $extensions, $recursive);
			$this->io->writeln('Files found: ' . count($targetFilepaths), OutputInterface::VERBOSITY_VERBOSE);
			$paths = array_merge($paths, $targetFilepaths);

			foreach ($targetFilepaths as $path)
			{
				$this->io->writeln('Found ' . $path, OutputInterface::VERBOSITY_VERY_VERBOSE);
			}
		}

		$infoOutput     = $this->getInfoOutput($output);
		$progressOutput = $this->getProgressOutput($output);
		$progressBar    = new ProgressBar($progressOutput);
		$progressBar->start(count($paths));

		$changed = [];
		foreach ($paths as $path)
		{
			$infoOutput->writeln('Patching ' . $path);
			if ($this->patcher->patchFile($path))
			{
				$changed[] = $path;
				$infoOutput->writeln('Updated ' . $path, OutputInterface::VERBOSITY_VERBOSE);
			}
			else
			{
				$infoOutput->writeln('Skipped ' . $path, OutputInterface::VERBOSITY_VERBOSE);
			}
			$progressBar->advance();
		}

		$this->io->success('Files changed: ' . count($changed) . ' / ' . count($paths));

		return Command::SUCCESS;
	}

	protected function getInfoOutput(OutputInterface $output): OutputInterface
	{
		if ($output instanceof ConsoleOutputInterface)
		{
			$infoOutput = $output->section();
			if (!$this->io->isVeryVerbose())
			{
				$infoOutput->setMaxHeight(1);
			}

			return $infoOutput;
		}

		return $output;
	}

	protected function getMarkupProcessorRepository(): MarkupProcessorRepository
	{
		$repository = new MarkupProcessorRepository;
		$repository->addProcessor(new Html);
		$repository->addProcessor(new Markdown);

		return $repository;
	}

	protected function getProgressOutput(OutputInterface $output): OutputInterface
	{
		return ($output instanceof ConsoleOutputInterface) ? $output->section() : $output;
	}

	protected function printSupportedFileExtensions(array $extensions): void
	{
		$rows = [];
		foreach ($extensions as $ext)
		{
			$rows[] = [$ext, get_class($this->repository->getProcessorForFileExtension($ext))];
		}

		$this->io->table(
			['File extension', 'Markup processor class'],
			$rows
		);
	}
}