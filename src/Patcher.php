<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use s9e\REPdoc\EvalImplementation\EvalInterface;

class Patcher
{
	public function __construct(
		public EvalInterface             $evalImplementation,
		public Filesystem                $filesystem,
		public MarkupProcessorRepository $processorRepository
	)
	{
	}

	/**
	* @param  string $path Path to the file
	* @return bool         Whether the file has changed
	*/
	public function patchFile(string $path): bool
	{
		$ext       = $this->filesystem->getFileExtension($path);
		$processor = $this->processorRepository->getProcessorForFileExtension($ext);
		if ($processor === false)
		{
			return false;
		}

		$old = $this->filesystem->read($path);
		$new = $processor->process($old, $this->evalImplementation);
		if ($old === $new)
		{
			return false;
		}
		$this->filesystem->write($path, $new);

		return true;
	}
}