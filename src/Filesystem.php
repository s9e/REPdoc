<?php declare(strict_types=1);

/**
* @package   s9e\REPdoc
* @copyright Copyright (c) The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\REPdoc;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use const PATHINFO_EXTENSION;
use function array_filter, file_get_contents, file_put_contents, in_array, is_dir, is_file, iterator_to_array, pathinfo;

/**
* This is a thin wrapper around PHP's file functions. It could theoretically be replaced with an
* abstraction layer such as league/flysystem
*/
class Filesystem
{
	public function getFileExtension(string $path): string
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	* @param  string   $path       Path to search in
	* @param  string[] $extensions Allowed file extensions
	* @param  bool     $recursive  Whether to recurse into directories
	* @return string[]             List of filepaths
	*/
	public function getFilepaths(string $path, array $extensions, bool $recursive): array
	{
		$paths = [];
		if (is_file($path))
		{
			$paths[] = $path;
		}
		elseif (is_dir($path) && $recursive)
		{
			return iterator_to_array(
				new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator(
						$path,
						FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS
					)
				)
			);
		}

		return $this->filterFileExtensions($paths);
	}

	public function read(string $path): string
	{
		return file_get_contents($path);
	}

	public function write(string $path, string $contents): void
	{
		if (file_put_contents($path, $contents) === false)
		{
			throw new RuntimeException('Cannot write to ' . $path);
		}
	}

	/**
	* @param  string[] $paths
	* @param  string[] $extensions
	* @return string[]
	*/
	protected function filterFileExtensions(array $paths, array $extensions): array
	{
		return array_filter(
			$paths,
			fn ($path) => in_array($this->getFileExtension($path), $extensions, true)
		);
	}
}