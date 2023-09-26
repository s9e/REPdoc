<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\MarkupProcessorRepository;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;
use s9e\REPdoc\Tests\Stubs\LoopbackMarkupProcessor;
use stdClass;

#[CoversClass('s9e\REPdoc\MarkupProcessorRepository')]
class MarkupProcessorRepositoryTest extends TestCase
{
	public function testConstructorRejectsInvalidArguments()
	{
		$this->expectException('InvalidArgumentException');
		$this->expectExceptionMessage('Cannot use stdClass as a markup processor');

		new MarkupProcessorRepository([new stdClass]);
	}

	public function testGetProcessorForFileExtension()
	{
		$processor  = new LoopbackMarkupProcessor(['foo' => 0, 'bar' => 0]);
		$repository = new MarkupProcessorRepository([$processor]);

		$this->assertSame($processor, $repository->getProcessorForFileExtension('foo'));
		$this->assertSame($processor, $repository->getProcessorForFileExtension('bar'));
		$this->assertFalse($repository->getProcessorForFileExtension('baz'));
	}

	public function testEmptyGetProcessorForFileExtension()
	{
		$repository = new MarkupProcessorRepository([]);

		$this->assertFalse($repository->getProcessorForFileExtension('baz'));
	}

	public function testEmptyMarkupProcessor()
	{
		$repository = new MarkupProcessorRepository([new LoopbackMarkupProcessor([])]);

		$this->assertFalse($repository->getProcessorForFileExtension('baz'));
	}

	public function testGetSupportedFileExtensions()
	{
		$repository = new MarkupProcessorRepository([
			new LoopbackMarkupProcessor(['foo' => 0, 'bar'  => 0]),
			new LoopbackMarkupProcessor(['baz' => 0, 'quux' => 0])
		]);

		$this->assertEquals(
			['bar', 'baz', 'foo', 'quux'],
			$repository->getSupportedFileExtensions()
		);
	}

	public function testGetPreferredProcessorForFileExtension()
	{
		$processorFoo = new LoopbackMarkupProcessor(['foo' => 10, 'bar' => 0]);
		$processorBar = new LoopbackMarkupProcessor(['foo' => 0, 'bar' => 10]);
		$repository = new MarkupProcessorRepository([$processorFoo, $processorBar]);

		$this->assertSame($processorFoo, $repository->getProcessorForFileExtension('foo'));
		$this->assertSame($processorBar, $repository->getProcessorForFileExtension('bar'));
	}
}