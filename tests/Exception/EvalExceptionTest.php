<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\Exception\EvalException;

#[CoversClass('s9e\REPdoc\Exception\EvalException')]
class EvalExceptionTest extends TestCase
{
	public function testSourceCode(): void
	{
		$e = new EvalException;
		$e->setSourceCode('echo $x;');

		$this->assertEquals('echo $x;', $e->getSourceCode());
	}
}