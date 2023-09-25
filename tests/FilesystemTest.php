<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use s9e\REPdoc\Filesystem;

/**
* @covers s9e\REPdoc\Filesystem
*/
class FilesystemTest extends TestCase
{
	protected $filesystem;

	public function setUp(): void
	{
		$this->filesystem = new Filesystem;
		vfsStream::setup('root');
	}

	public function testRead()
	{
		$id = uniqid('');

		$filepath = vfsStream::url('root/foo.txt');
		file_put_contents($filepath, $id);

		$this->assertEquals($id, $this->filesystem->read($filepath));
	}

	public function testWrite()
	{
		$id = uniqid('');

		$filepath = vfsStream::url('root/foo.txt');
		$this->filesystem->write($filepath, $id);

		$this->assertStringEqualsFile($filepath, $id);
	}
}