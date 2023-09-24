<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\EvalImplementation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\EvalImplementation\NativeEval;
use s9e\REPdoc\EvalImplementation\EvalInterface;

/**
* @covers s9e\REPdoc\EvalImplementation\NativeEval
*/
class NativeEvalTest extends TestCase
{
	public function testError()
	{
		$this->expectException('ArgumentCountError');
		(new NativeEval)('substr();');
	}

	#[DataProvider('getNativeEvalTests')]
	public function testEval(string $code, string $expected)
	{
		$eval = new NativeEval;

		$this->assertEquals($expected, $eval($code));
	}

	public static function getNativeEvalTests(): array
	{
		return [
			[
				'',
				''
			],
			[
				'echo "Hello world.";',
				'Hello world.'
			],
		];
	}
}