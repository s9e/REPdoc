<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\MarkupProcessor;

use PHPUnit\Framework\Attributes\CoversClass;
use s9e\REPdoc\MarkupProcessor\Markdown;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

#[CoversClass('s9e\REPdoc\MarkupProcessor\Markdown')]
class MarkdownTest extends AbstractMarkupProcessorTestCase
{
	protected function getMarkupProcessor(): MarkupProcessorInterface
	{
		return new Markdown;
	}

	public function testSupportsFileExtension()
	{
		$this->assertContains('md', (new Markdown)->getSupportedFileExtensions());
	}
}