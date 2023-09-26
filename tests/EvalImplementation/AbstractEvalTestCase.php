<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\EvalImplementation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\EvalImplementation\EvalInterface;

#[DoesNotPerformAssertions]
abstract class AbstractEvalTestCase extends TestCase
{
	abstract protected function getEvalImplementation(): EvalInterface;

	#[DataProvider('getSuccesfulEvalTests')]
	public function testEval(string $code, string $expected)
	{
		$eval = $this->getEvalImplementation();

		$this->assertEquals($expected, $eval($code));
	}

	public static function getSuccesfulEvalTests(): array
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
			[
				'var_dump(class_exists(' . var_export(__CLASS__, true) . '));',
				"bool(true)\n"
			],
		];
	}
}