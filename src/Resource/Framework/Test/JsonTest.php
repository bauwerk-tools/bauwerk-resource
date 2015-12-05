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

namespace ApparatTest;

use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Part\JsonPart;
use Apparat\Resource\Framework\Resource\JsonResource;

\date_default_timezone_set('UTC');

/**
 * JSON file tests
 *
 * @package ApparatTest
 */
class JsonTest extends TestBase
{
	/**
	 * Example JSON data
	 *
	 * @var string
	 */
	protected $_json = null;

	/**
	 * Example JSON file
	 *
	 * @var string
	 */
	const JSON_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.json';

	/**
	 * Sets up the fixture
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_json = file_get_contents(self::JSON_FILE);
	}

	/**
	 * Test the JSON file constructor
	 */
	public function testJsonResource()
	{
		$jsonResource = new JsonResource();
		$this->assertInstanceOf(JsonResource::class, $jsonResource);
		$this->assertEquals(JsonPart::MIME_TYPE, $jsonResource->getMimeTypePart());
	}

	/**
	 * Test the JSON file constructor with reader
	 */
	public function testJsonResourceReader()
	{
		$jsonResource = new JsonResource(new Reader($this->_json));
		$this->assertEquals($this->_json, $jsonResource->getPart());
	}

	/**
	 * Test getting the data of a JSON file with unallowed subparts
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testJsonResourceHtmlSubparts()
	{
		$jsonResource = new JsonResource(new Reader($this->_json));
		$jsonResource->getDataPart('a/b/c');
	}

	/**
	 * Test getting the data of a Json file
	 */
	public function testJsonResourceGetData()
	{
		$expectedData = include __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.php';
		$jsonResource = new JsonResource(new Reader($this->_json));
		$this->assertArrayEquals($expectedData, $jsonResource->getDataPart());
	}

	/**
	 * Test getting the data of a Json file
	 */
	public function testJsonResourceSetData()
	{
		// Prepare modified expected data
		$expectedData = include __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.php';
		$expectedData['date'] = time();
		$expectedData['bill-to']['given'] = 'John';
		$expectedData['bill-to']['family'] = 'Doe';
		$expectedData['product'][] = [
			'sku' => 'ABC123',
			'quantity' => 1,
			'description' => 'Dummy',
			'price' => 123
		];
		unset($expectedData['comments']);
		$jsonResource = new JsonResource(new Reader($this->_json));
		$jsonResource->setDataPart($expectedData);
		$this->assertArrayEquals($expectedData, $jsonResource->getDataPart());
	}
}