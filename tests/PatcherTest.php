<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use s9e\REPdoc\EvalImplementation\NativeEval;
use s9e\REPdoc\Filesystem;
use s9e\REPdoc\MarkupProcessorRepository;
use s9e\REPdoc\MarkupProcessor\Markdown;
use s9e\REPdoc\Patcher;

#[CoversClass('s9e\REPdoc\Patcher')]
class PatcherTest extends TestCase
{
	#[DataProvider('getPatchFileTests')]
	public function testPatchFile(array $files)
	{
		vfsStream::setup('root');

		$repository = new MarkupProcessorRepository;
		$repository->addProcessor(new Markdown);

		$patcher = new Patcher(
			evalImplementation:  new NativeEval,
			filesystem:          new Filesystem,
			processorRepository: $repository
		);
		foreach ($files as $filename => [$original, $expected])
		{
			$original = str_replace("\n\t\t\t\t\t\t", "\n", $original);
			$expected = str_replace("\n\t\t\t\t\t\t", "\n", $expected);

			$path    = vfsStream::url('root/' . $filename);
			$changed = ($original !== $expected);
			file_put_contents($path, $original);

			$this->assertSame($changed, $patcher->patchFile($path));
			$this->assertStringEqualsFile($path, $expected);
		}
	}

	public static function getPatchFileTests(): array
	{
		return [
			[
				['foo.exe' => ['', '']]
			],
			[
				[
					'foo.md' => [
						'..

						```php
						echo "Hello world.";
						```
						```plain
						...
						```',
						'..

						```php
						echo "Hello world.";
						```
						```plain
						Hello world.
						```'
					]
				]
			],
			[
				[
					'foo.md' => [
						'..

						```php
						echo "Hello world.";
						```
						```plain
						Hello world.
						```',
						'..

						```php
						echo "Hello world.";
						```
						```plain
						Hello world.
						```'
					]
				]
			],
		];
	}
}