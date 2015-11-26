<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Apparat\Resource\Framework\Io;

use Apparat\Resource\Framework\Io\File\AbstractFileReaderWriter;
use Apparat\Resource\Framework\Io\File\Reader as FileReader;
use Apparat\Resource\Framework\Io\File\Writer as FileWriter;
use Apparat\Resource\Framework\Io\InMemory\AbstractInMemoryReaderWriter;
use Apparat\Resource\Framework\Io\InMemory\Reader as InMemoryReader;
use Apparat\Resource\Framework\Io\InMemory\Writer as InMemoryWriter;
use Apparat\Resource\Model\Reader;
use Apparat\Resource\Model\Writer;

/**
 * Reader / writer utilities
 *
 * @package Apparat\Resource\Framework\Io
 */
class Io
{
	/**
	 * Reader classes for stream wrappers
	 *
	 * @var array
	 */
	public static $reader = array(
		AbstractFileReaderWriter::WRAPPER => FileReader::class,
		AbstractInMemoryReaderWriter::WRAPPER => InMemoryReader::class,
	);

	/**
	 * Writer classes for stream wrappers
	 *
	 * @var array
	 */
	public static $writer = array(
		AbstractFileReaderWriter::WRAPPER => FileWriter::class,
		AbstractInMemoryReaderWriter::WRAPPER => InMemoryWriter::class,
	);

	/**
	 * Find and instantiate a reader for a particular source
	 *
	 * @param string $src Source
	 * @param array $parameters Parameters
	 * @return null|Reader  Reader instance
	 */
	public static function reader(&$src, array $parameters = array())
	{
		$reader = null;

		// Run through all registered readers
		foreach (self::$reader as $wrapper => $readerClass) {
			$wrapperLength = strlen($wrapper);

			// If this wrapper is used: Instantiate the reader and resource
			if ($wrapperLength ? !strncmp($wrapper, $src, $wrapperLength) : !preg_match("%^[a-z0-9\.]+\:\/\/%", $src)) {
				$src = substr($src, $wrapperLength);
				$reader = new $readerClass($src, ...$parameters);
				break;
			}
		}

		return $reader;
	}

	/**
	 * Find and instantiate a writer for a particular target
	 *
	 * @param string $target Target
	 * @param array $parameters Parameters
	 * @return null|Writer  Writer instance
	 */
	public static function writer(&$target, array $parameters = array())
	{
		$writer = null;

		// Run through all registered writer
		foreach (self::$writer as $wrapper => $writerClass) {
			$wrapperLength = strlen($wrapper);

			// If this wrapper is used: Instantiate the reader and resource
			if ($wrapperLength ? !strncmp($wrapper, $target, $wrapperLength) : !preg_match("%^[a-z0-9\.]+\:\/\/%",
				$target)
			) {
				$target = substr($target, $wrapperLength);
				$writer = new $writerClass($target, ...$parameters);
				break;
			}
		}

		return $writer;
	}

	/**
	 * Copy a resource
	 *
	 * @param string $src Stream-wrapped source
	 * @param array ...$parameters Reader parameters
	 * @return Copy Copy handler
	 */
	public static function copy($src, ...$parameters)
	{
		$reader = self::reader($src, $parameters);
		if ($reader instanceof Reader) {
			return new Copy($reader);
		}

		throw new InvalidArgumentException('Invalid reader stream wrapper',
			InvalidArgumentException::INVALID_READER_STREAM_WRAPPER);
	}

	/**
	 * Move / rename a resource
	 *
	 * @param string $src Stream-wrapped source
	 * @param array ...$parameters Reader parameters
	 * @return Move move handler
	 */
	public static function move($src, ...$parameters)
	{
		$reader = self::reader($src, $parameters);
		if ($reader instanceof Reader) {
			return new Move($reader);
		}

		throw new InvalidArgumentException('Invalid reader stream wrapper',
			InvalidArgumentException::INVALID_READER_STREAM_WRAPPER);
	}
}