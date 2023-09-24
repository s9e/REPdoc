<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use InvalidArgumentException;
use function usort;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

class MarkupProcessorRepository
{
	/**
	* @var array<string, MarkupProcessorInterface> Processor for each supported file extension
	*/
	public array $processors = [];

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
				$filetypes[$ext][] = ['score' => (int) $score, 'processor' => $processor];
			}
		}

		foreach ($filetypes as $ext => $processors)
		{
			// Sort by descending score, then use the highest scored processor
			usort($processors, fn($a, $b) => $b['score'] - $a['score']);

			$this->processors[$ext] = $processors[0]['processor'];
		}
	}
}