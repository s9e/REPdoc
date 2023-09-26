<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use InvalidArgumentException;
use const SORT_NUMERIC;
use function array_keys, end, ksort, usort;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

class MarkupProcessorRepository
{
	/**
	* @var array<string, MarkupProcessorInterface> Processor for each supported file extension
	*/
	protected array $processors = [];

	/**
	* @param MarkupProcessorInterface[] $processors
	*/
	public function __construct(array $processors)
	{
		$filetypes = [];
		foreach ($processors as $processor)
		{
			if (!($processor instanceof MarkupProcessorInterface))
			{
				throw new InvalidArgumentException('Cannot use ' . $processor::class . ' as a markup processor');
			}

			foreach ($processor->getSupportedFileExtensions() as $ext => $score)
			{
				$filetypes[$ext][$score] = $processor;
			}
		}

		foreach ($filetypes as $ext => $processors)
		{
			// Sort processors by score ascending, then grab the last one
			ksort($processors, SORT_NUMERIC);

			$this->processors[$ext] = end($processors);
		}
		ksort($this->processors);
	}

	public function getProcessorForFileExtension(string $ext): MarkupProcessorInterface|false
	{
		return $this->processors[$ext] ?? false;
	}

	/**
	* @return string[]
	*/
	public function getSupportedFileExtensions(): array
	{
		return array_keys($this->processors);
	}
}