<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
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
	public function testExecute(array $originalFiles, array $expectedFiles, array $input = [], array $options = [], string $expectedOutput = null): void
	{
		vfsStream::setup('root');
		foreach ($originalFiles as $path => $contents)
		{
			$url = vfsStream::url('root/' . $path);
			file_put_contents($url, $contents);
		}

		$input['targets'] = array_map(
			fn($path) => vfsStream::url('root/' . $path),
			$input['targets'] ?? array_keys($originalFiles)
		);
		

		$commandTester = new CommandTester(new Patch);
		$commandTester->execute($input, $options);
		foreach ($expectedFiles as $path => $contents)
		{
			$url = vfsStream::url('root/' . $path);
			$this->assertStringEqualsFile($url, $contents);
		}

		$commandTester->assertCommandIsSuccessful();

		if (isset($expectedOutput))
		{
			$this->assertStringContainsString($expectedOutput, $commandTester->getDisplay());
		}
	}

	public static function getExecuteTests(): array
	{
		return [
			[
				['foo.md' => '...'],
				['foo.md' => '...']
			],
			[
				['foo.md' => '
```php
echo str_rot13("Grfg cnffrq.");
```
```
```
				'],
				['foo.md' => '
```php
echo str_rot13("Grfg cnffrq.");
```
```
Test passed.
```
				']
			],
			[
				['foo.md' => '...'],
				['foo.md' => '...'],
				[],
				['verbosity' => OutputInterface::VERBOSITY_VERBOSE],
				'File extension'
			],
		];
	}
}