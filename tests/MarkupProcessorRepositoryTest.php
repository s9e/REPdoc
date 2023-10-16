<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\MarkupProcessorRepository;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;
use s9e\REPdoc\Tests\Stubs\LoopbackMarkupProcessor;

#[CoversClass('s9e\REPdoc\MarkupProcessorRepository')]
class MarkupProcessorRepositoryTest extends TestCase
{
	public function testGetProcessorForFileExtension()
	{
		$processor  = new LoopbackMarkupProcessor(['foo', 'bar']);
		$repository = new MarkupProcessorRepository;
		$repository->addProcessor($processor);

		$this->assertSame($processor, $repository->getProcessorForFileExtension('foo'));
		$this->assertSame($processor, $repository->getProcessorForFileExtension('bar'));
		$this->assertNull($repository->getProcessorForFileExtension('baz'));
	}

	public function testEmptyGetProcessorForFileExtension()
	{
		$repository = new MarkupProcessorRepository;

		$this->assertNull($repository->getProcessorForFileExtension('baz'));
	}

	public function testEmptyMarkupProcessor()
	{
		$repository = new MarkupProcessorRepository;
		$repository->addProcessor(new LoopbackMarkupProcessor([]));

		$this->assertNull($repository->getProcessorForFileExtension('baz'));
	}

	public function testGetSupportedFileExtensions()
	{
		$repository = new MarkupProcessorRepository;
		$repository->addProcessor(new LoopbackMarkupProcessor(['foo', 'bar']));
		$repository->addProcessor(new LoopbackMarkupProcessor(['baz', 'quux']));

		$this->assertEquals(
			['bar', 'baz', 'foo', 'quux'],
			$repository->getSupportedFileExtensions()
		);
	}
}