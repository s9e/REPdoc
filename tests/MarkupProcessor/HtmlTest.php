<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\MarkupProcessor;

use PHPUnit\Framework\Attributes\CoversClass;
use s9e\REPdoc\MarkupProcessor\Html;
use s9e\REPdoc\MarkupProcessor\MarkupProcessorInterface;

#[CoversClass('s9e\REPdoc\MarkupProcessor\Html')]
class HtmlTest extends AbstractMarkupProcessorTestCase
{
	protected function getMarkupProcessor(): MarkupProcessorInterface
	{
		return new Html;
	}

	public function testSupportsFileExtension()
	{
		$this->assertContains('html', (new Html)->getSupportedFileExtensions());
	}
}