<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\MarkupProcessor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\EvalImplementation\NativeEval;
use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

abstract class AbstractMarkupProcessorTestCase extends TestCase
{
	abstract protected function getMarkupProcessor(): MarkupProcessorInterface;

	#[DataProvider('getMarkupTests')]
	public function testProcess(string $filepath, string $original, EvalInterface $eval = new NativeEval)
	{
		$processor = $this->getMarkupProcessor();

		$this->assertStringEqualsFile($filepath, $processor->process($original, $eval));
	}

	public static function getMarkupTests(): array
	{
		$dir = __DIR__ . '/data/' . preg_replace('(.*?(\\w+)Test$)', '$1', static::class);

		$tests = [];
		foreach (glob($dir . '/*.expected.*') as $filepath)
		{
			$tests[] = [
				$filepath,
				file_get_contents(str_replace('.expected.', '.original.', $filepath))
			];
		}

		return $tests;
	}
}