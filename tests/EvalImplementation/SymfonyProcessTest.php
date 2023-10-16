<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\EvalImplementation;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\EvalImplementation\SymfonyProcess;

#[CoversClass('s9e\REPdoc\EvalImplementation\SymfonyProcess')]
class SymfonyProcessTest extends AbstractEvalTestCase
{
	protected function getEvalImplementation(): EvalInterface
	{
		return new SymfonyProcess;
	}
}