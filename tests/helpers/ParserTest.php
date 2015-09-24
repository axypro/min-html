<?php

namespace axy\min\html\tests;

use axy\min\html\HTMLMinifier;
use axy\min\html\helpers\Parser;

/**
 * coversDefaultClass axy\min\html\helpers\Parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::parseLint
     * @dataProvider providerParseLine
     * @param string $line
     * @param array $tags
     * @param string $before
     * @param string $after
     * @param string $tag
     */
    public function testParseLine($line, $tags, $before, $after, $tag)
    {
        $tags = HTMLMinifier::getDefaultsTags($tags);
        $result = Parser::parseLine($line, $tags);
        $this->assertInstanceOf('axy\min\html\helpers\ParseLineResult', $result);
        $this->assertSame($before, $result->before);
        $this->assertSame($after, $result->after);
        $this->assertSame($tag, $result->tag);
    }

    /**
     * @return array
     */
    public function providerParseLine()
    {
        return [
            'normal' => [
                '    <p style="color:red"><span>X</span>  Y</p>   ',
                null,
                '    <p style="color:red"><span>X</span>  Y</p>',
                null,
                null,
            ],
            'enclose' => [
                'x: <p><span>X</span> <pre> Y </PRE></p>   ',
                null,
                'x: <p><span>X</span> <pre> Y </PRE></p>',
                null,
                null,
            ],
            'disable' => [
                '<p><span>X</span> <pre> Y </pre></p>   ',
                [
                    'pre' => null,
                ],
                '<p><span>X</span> <pre> Y </pre></p>',
                null,
                null,
            ],
            'callback' => [
                '<p><span>X</span> <pre> Y </pre></p>   ',
                [
                    'pre' => function ($content) {
                        return '--'.$content.'--';
                    },
                ],
                '<p><span>X</span> <pre>-- Y --</pre></p>',
                null,
                null,
            ],
            'out' => [
                '<p><span>X</span> <pre> Y </p>    ',
                null,
                '<p><span>X</span> <pre>',
                ' Y </p>    ',
                'pre',
            ],
            'case' => [
                '<p><span>X</span> <PRE> Y </p>    ',
                null,
                '<p><span>X</span> <PRE>',
                ' Y </p>    ',
                'pre',
            ],
            'enclose2' => [
                'x: <p><span>X</span> <pre> Y </PRE> x <pre>  Z </pre></p>    ',
                [
                    'pre' => function ($content) {
                        return '--'.$content.'--';
                    },
                ],
                'x: <p><span>X</span> <pre>-- Y --</PRE> x <pre>--  Z --</pre></p>',
                null,
                null,
            ],
            'out2' => [
                'x: <p><span>X</span> <pre> Y </PRE> x <pre>  Z </p>    ',
                [
                    'pre' => function ($content) {
                        return '--'.$content.'--';
                    },
                ],
                'x: <p><span>X</span> <pre>-- Y --</PRE> x <pre>',
                '  Z </p>    ',
                'pre',
            ],
        ];
    }

    /**
     * covers ::process
     */
    public function testProcess()
    {
        $f = function ($content) {
            return '-'.$content.'-';
        };
        $this->assertSame('  one  ', Parser::process('  one  ', true));
        $this->assertSame("  one  \n  two  ", Parser::process(['  one  ', '  two  '], true));
        $this->assertSame('-  one  -', Parser::process('  one  ', $f));
        $this->assertSame("-  one  \n  two  -", Parser::process(['  one  ', '  two  '], $f));
    }

    /**
     * covers ::parseLint
     * @dataProvider providerFindClose
     * @param string $line
     * @param string $tag
     * @param string $before
     * @param string $after
     * @param string $rTag
     */
    public function testFindClose($line, $tag, $before, $after, $rTag)
    {
        $result = Parser::findClose($line, $tag);
        $this->assertInstanceOf('axy\min\html\helpers\ParseLineResult', $result);
        $this->assertSame($before, $result->before);
        $this->assertSame($after, $result->after);
        $this->assertSame($rTag, $result->tag);
    }

    /**
     * @return array
     */
    public function providerFindClose()
    {
        return [
            'no' => [
                '<p> a <pre> b </p>',
                'pre',
                '<p> a <pre> b </p>',
                null,
                null,
            ],
            'yes' => [
                '<p> a </pre> <pre> b </pre> </p>',
                'pre',
                '<p> a ',
                '</pre> <pre> b </pre> </p>',
                'pre',
            ],
            'case' => [
                '<p> a </PRE> <pre> b </pre> </p>',
                'pre',
                '<p> a ',
                '</PRE> <pre> b </pre> </p>',
                'pre',
            ],
        ];
    }
}
