<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use function array_keys;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

class MarkupProcessorRepository
{
	/**
	* @var array<string, MarkupProcessorInterface>
	*/
	protected array $processors = [];

	public function addProcessor(MarkupProcessorInterface $processor): void
	{
		foreach ($processor->getSupportedFileExtensions() as $ext)
		{
			$this->processors[$ext] = $processor;
		}
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