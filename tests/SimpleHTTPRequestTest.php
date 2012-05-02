<?php
/**
 *
 *
 */

require_once dirname(dirname(__FILE__)) . '/SimpleHTTPRequest.php';

class SimpleHTTPRequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddFileException()
    {
        $request = new SimpleHTTPRequest();
        $request->addFile('file', 'dummy.dummy');
    }

}
