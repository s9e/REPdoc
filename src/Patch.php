<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'repdoc:patch', description: 'Patches target files and directories')]
class Patch extends Command
{
	protected function configure(): void
	{
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
		$output->write(var_export($input->getOption('recursive'), true));

		return Command::SUCCESS;
	}
}