<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\EvalImplementation;

use PHPUnit\Framework\Attributes\CoversClass;
use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\EvalImplementation\NativeEval;

#[CoversClass('s9e\REPdoc\EvalImplementation\NativeEval')]
class NativeEvalTest extends AbstractEvalTestCase
{
	protected function getEvalImplementation(): EvalInterface
	{
		return new NativeEval;
	}
}