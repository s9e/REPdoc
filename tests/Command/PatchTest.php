<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;
use s9e\REPdoc\Command\Patch;

#[CoversClass('s9e\REPdoc\Command\Patch')]
class PatchTest extends TestCase
{
	public function testRequiresTargets()
	{
		$this->expectException('RuntimeException');
		$this->expectExceptionMessage('Not enough arguments (missing: "targets")');

		$commandTester = new CommandTester(new Patch);
		$commandTester->execute([]);
	}

	#[DataProvider('getExecuteTests')]
	public function testExecute(array $originalFiles, array $expectedFiles, array $targets = null): void
	{
		vfsStream::setup('root');
		chdir('root');
		foreach ($originalFiles as $path => $contents)
		{
			file_put_contents($path, $contents);
		}

		$targets ??= array_keys($originalFiles);

		$commandTester = new CommandTester(new Patch);
		$commandTester->execute([
			'targets' => $targets
		]);
	}

	public function getExecuteTests(): array
	{
		return [
		];
	}
}