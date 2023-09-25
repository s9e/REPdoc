<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests\MarkupProcessor;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use s9e\REPdoc\EvalImplementation\NativeEval;
use s9e\REPdoc\EvalImplementation\EvalInterface;
use s9e\REPdoc\MarkupProcessor\Markdown;

#[CoversClass('s9e\REPdoc\MarkupProcessor\Markdown')]
class MardownTest extends TestCase
{
	public function testSupportsFileExtension()
	{
		$this->assertArrayHasKey('md', (new Markdown)->getSupportedFileExtensions());
	}

	#[DataProvider('getMarkdownTests')]
	public function testProcess(string $original, string $expected, EvalInterface $eval = new NativeEval)
	{
		$processor = new Markdown;

		$this->assertEquals($expected, $processor->process($original, $eval));
	}

	public static function getMarkdownTests(): array
	{
		return [
			[
				'',
				''
			],
			[
				'Text.

```php
echo "Hello world.";
```
```
```
',
				'Text.

```php
echo "Hello world.";
```
```
Hello world.
```
'
			],
			[
				'```php
echo "block #1";
```
```plain
```
```php
echo "<b>block #2</b>";
```
```html
```',
				'```php
echo "block #1";
```
```plain
block #1
```
```php
echo "<b>block #2</b>";
```
```html
<b>block #2</b>
```'
			],
			[
				'````php
echo "Hello world.";
````
`````
Hello world.
`````',
				'````php
echo "Hello world.";
````
`````
Hello world.
`````'
			],
			[
				'~~~~php
echo "Hello world.";
~~~~
~~~~~
Hello world.
~~~~~',
				'~~~~php
echo "Hello world.";
~~~~
~~~~~
Hello world.
~~~~~'
			],
		];
	}
}