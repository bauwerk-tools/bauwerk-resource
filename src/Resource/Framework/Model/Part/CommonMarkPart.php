<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Apparat\Resource\Framework\Model\Part;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

/**
 * CommonMark resource part
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class CommonMarkPart extends TextPart
{
    /**
     * Mime type
     *
     * @var string
     */
    const MIME_TYPE = 'text/x-markdown';

    /**
     * Convert the CommonMark source to HTML
     *
     * @return string CommonMark HTML
     */
    public function getHtml()
    {
        $html = '';

        if (strlen($this->_content)) {
            $environment = $this->_environment();
            $parser = new DocParser($environment);
            $renderer = new HtmlRenderer($environment);
            $html = $renderer->renderBlock($parser->parse($this->_content));
        }

        return $html;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Create and return a CommonMark environment
     *
     * @return Environment CommonMark environment
     */
    protected function _environment()
    {

        // Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
        $environment = Environment::createCommonMarkEnvironment();

        // Custom environment initialization
        $this->_initializeEnvironment($environment);

        return $environment;
    }

    /**
     * Custom environment initialization
     *
     * Overwrite this method in subclasses to register your own parsers/renderers.
     *
     * @param Environment $environment
     */
    protected function _initializeEnvironment(Environment $environment)
    {
        // Optional: Add your own parsers/renderers here, if desired
        // For example:  $environment->addInlineParser(new TwitterHandleParser());
    }
}
