<?php

namespace axy\min\html\tests;

use axy\min\html\HTMLMinifier;

/**
 * coversDefaultClass axy\min\html\HTMLMinifier
 */
class HTMLMinifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::compress
     */
    public function testNormal()
    {
        $data = $this->loadFile('normal');
        $this->assertSame($data->expected, HTMLMinifier::compress($data->origin));
    }

    /**
     * covers ::compress
     */
    public function testTags()
    {
        $data = $this->loadFile('tags');
        $tags = [
            'script' => function ($content) {
                return preg_replace('/\s+/', ' ', trim($content));
            },
            'textarea' => null,
            'style' => true,
        ];
        $this->assertSame($data->expected, HTMLMinifier::compress($data->origin, $tags));
    }

    /**
     * covers ::run
     * covers ::getOriginContent
     * covers ::getCompressedContent
     * covers ::getTags
     */
    public function testNotStatic()
    {
        $data = $this->loadFile('tags');
        $tags = [
            'script' => function ($content) {
                return preg_replace('/\s+/', ' ', trim($content));
            },
            'textarea' => null,
            'style' => true,
        ];
        $minifier = new HTMLMinifier($data->origin, $tags);
        $this->assertSame($data->expected, $minifier->run());
        $this->assertSame($data->expected, $minifier->getCompressedContent());
        $this->assertSame($data->expected, $minifier->run());
        $this->assertSame($data->origin, $minifier->getOriginContent());
        $tagsExpected = $tags;
        $tagsExpected['pre'] = true;
        $this->assertEquals($tagsExpected, $minifier->getTags());
    }

    /**
     * covers ::compressFromFile
     */
    public function testCompressFromFile()
    {
        $expected = "<html>\n<title>X</title>\n</html>";
        $actual = trim(HTMLMinifier::compressFromFile(__DIR__.'/files/f.html'));
        $this->assertSame($expected, $actual);
    }

    /**
     * covers ::compressFile
     */
    public function testCompressFile()
    {
        $expected = "<html>\n<title>X</title>\n</html>";
        $destination = __DIR__.'/tmp/f.html';
        if (is_file($destination)) {
            unlink($destination);
        }
        $result = HTMLMinifier::compressFile(__DIR__.'/files/f.html', $destination);
        $this->assertSame($expected, trim($result));
        $this->assertFileExists($destination);
        $actual = trim(file_get_contents($destination));
        $this->assertSame($expected, $actual);
    }

    /**
     * @param string $name
     * @return object
     */
    private function loadFile($name)
    {
        if (!isset($this->cacheFiles[$name])) {
            $fn = __DIR__ . '/files/' . $name . '.html';
            $content = file_get_contents($fn);
            $content = explode('-----', $content, 2);
            return (object)[
                'origin' => trim($content[0]),
                'expected' => trim($content[1]),
            ];
        }
        return $this->cacheFiles[$name];
    }

    /**
     * @var object[]
     */
    private $cacheFiles = [];
}
