<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use const SORT_STRING;
use function get_class, sort;
use s9e\REPdoc\MarkupProcessorRepository;
use s9e\REPdoc\MarkupProcessor\Html;
use s9e\REPdoc\MarkupProcessor\Markdown;

#[AsCommand(name: 'repdoc:list', description: 'Lists supported file extensions')]
class ListMarkup extends Command
{
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$repository = new MarkupProcessorRepository;
		$repository->addProcessor(new Html);
		$repository->addProcessor(new Markdown);

		$extensions = $repository->getSupportedFileExtensions();
		sort($extensions, SORT_STRING);

		$rows = [];
		foreach ($extensions as $ext)
		{
			$rows[] = [$ext, get_class($repository->getProcessorForFileExtension($ext))];
		}

		$io->table(
			['File extension', 'Markup processor class'],
			$rows
		);

		return Command::SUCCESS;
	}
}