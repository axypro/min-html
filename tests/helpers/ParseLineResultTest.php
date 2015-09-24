<?php

namespace axy\min\html\tests;

use axy\min\html\helpers\ParseLineResult;

/**
 * coversDefaultClass axy\min\html\helpers\ParseLineResult
 */
class ParseLineResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::append
     */
    public function testAppend()
    {
        $result = new ParseLineResult('123', 'tg');
        $result->append('456');
        $result->append('789');
        $this->assertSame('123456789', $result->before);
        $this->assertSame('tg', $result->tag);
    }
}
