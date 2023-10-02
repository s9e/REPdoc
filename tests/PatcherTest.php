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
use s9e\REPdoc\Patcher;

#[CoversClass('s9e\REPdoc\Patcher')]
class PatcherTest extends TestCase
{
	#[DataProvider('getPatchFileTests')]
	public function testPatchFile(array $originalFiles, array $expectedFiles)
	{
		vfsStream::setup('root');
		foreach ($originalFiles as $filename => $contents)
		{
			file_put_contents(vfsStream::url('root/' . $filename), $contents);
		}

		$patcher = new Patcher(
			evalImplementation:  new NativeEval,
			filesystem:          new Filesystem,
			processorRepository: new MarkupProcessorRepository
		);
		foreach ($expectedFiles as $filename => $contents)
		{
			$this->assertStringEqualsFile(vfsStream::url('root/' . $filename), $contents);
		}
	}

	public static function getPatchFileTests(): array
	{
		return [
			[
				['foo.exe' => ''],
				['foo.exe' => '']
			],
		];
	}
}