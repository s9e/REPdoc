<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;
use s9e\REPdoc\Command\Patch;
use s9e\REPdoc\Tests\Stubs\PatchProxy;

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

	public function testRequiresSymfonyProcess()
	{
		$command = new PatchProxy;
		$command->setSymfonyProcess(false);

		$commandTester = new CommandTester($command);
		$commandTester->execute([
			'--process-isolation' => true,
			'targets'             => []
		]);

		$this->assertEquals($command::FAILURE, $commandTester->getStatusCode());
	}

	#[DataProvider('getExecuteTests')]
	public function testExecute(array $originalFiles, array $expectedFiles, array $targets = null): void
	{
		vfsStream::setup('root');
		foreach ($originalFiles as $path => $contents)
		{
			$url = vfsStream::url('root/' . $path);
			file_put_contents($url, $contents);
		}

		$targets = array_map(
			fn($path) => vfsStream::url('root/' . $path),
			$targets ?? array_keys($originalFiles)
		);

		$commandTester = new CommandTester(new Patch);
		$commandTester->execute([
			'targets' => $targets
		]);
		foreach ($expectedFiles as $path => $contents)
		{
			$url = vfsStream::url('root/' . $path);
			$this->assertStringEqualsFile($url, $contents);
		}

		$commandTester->assertCommandIsSuccessful();
	}

	public static function getExecuteTests(): array
	{
		return [
			[
				['foo.md' => '...'],
				['foo.md' => '...']
			],
		];
	}
}