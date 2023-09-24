<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

class Patcher
{
	public function __construct(public MarkupProcessorRepository $processorRepository)
	{
	}

	/**
	* 
	*
	* @return void
	*/
	public function patchFile(string $path)
	{
	}
}