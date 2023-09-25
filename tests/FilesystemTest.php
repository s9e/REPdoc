<?php declare(strict_types=1);

namespace s9e\REPdoc\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use s9e\REPdoc\Filesystem;

#[CoversClass('s9e\REPdoc\Filesystem')]
class FilesystemTest extends TestCase
{
	protected $filesystem;

	public function setUp(): void
	{
		$this->filesystem = new Filesystem;
	}

	public function testGetFileExtension()
	{
		$this->assertEquals('md',  $this->filesystem->getFileExtension('/foo/bar/README.md'));
		$this->assertEquals('txt', $this->filesystem->getFileExtension('file:///tmp/foo.txt'));
	}

	public function testGetFilepathsDir()
	{
		vfsStream::setup('root');
		touch(vfsStream::url('root/foo.txt'));
		touch(vfsStream::url('root/bar.md'));
		touch(vfsStream::url('root/baz.rst'));

		$this->assertEquals(
			[vfsStream::url('root/bar.md'), vfsStream::url('root/baz.rst')],
			$this->filesystem->getFilepaths(vfsStream::url('root'), ['md', 'rst'], true)
		);
	}

	public function testGetFilepathsFile()
	{
		vfsStream::setup('root');
		touch(vfsStream::url('root/foo.txt'));
		touch(vfsStream::url('root/bar.md'));
		touch(vfsStream::url('root/baz.rst'));

		$this->assertEquals(
			[vfsStream::url('root/bar.md')],
			$this->filesystem->getFilepaths(vfsStream::url('root/bar.md'), ['md', 'rst'], true)
		);
	}

	public function testRead()
	{
		$id = uniqid('');

		vfsStream::setup('root');
		$filepath = vfsStream::url('root/foo.txt');
		file_put_contents($filepath, $id);

		$this->assertEquals($id, $this->filesystem->read($filepath));
	}

	public function testWrite()
	{
		$id = uniqid('');

		vfsStream::setup('root');
		$filepath = vfsStream::url('root/foo.txt');
		$this->filesystem->write($filepath, $id);

		$this->assertStringEqualsFile($filepath, $id);
	}

	public function testWriteFail()
	{
		vfsStream::setup('root');
		$filepath = vfsStream::url('root/file.txt');

		$this->expectException('RuntimeException');
		$this->expectExceptionMessage('Cannot write to ' . $filepath);

		chmod(vfsStream::url('root'), 0000);
		@$this->filesystem->write($filepath, '');
	}
}