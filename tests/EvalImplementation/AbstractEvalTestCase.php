<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\EvalImplementation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\Exception\EvalException;

abstract class AbstractEvalTestCase extends TestCase
{
	abstract protected function getEvalImplementation(): EvalInterface;

	public function testExceptionIsThrownOnInvalidCode()
	{
		$this->expectException(EvalException::class);
		$this->getEvalImplementation()('substr();');
	}

	public function testExceptionHasSourceCode()
	{
		try
		{
			$this->getEvalImplementation()('substr();');
		}
		catch (EvalException $e)
		{
			$this->assertStringContainsString('substr();', $e->getSourceCode());
		}
	}

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