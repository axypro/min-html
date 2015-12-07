<?php
/**
 * @package axy\min\html
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\min\html;

use axy\min\html\helpers\Parser;

/**
 * Compressor of HTML code
 *
 * Argument $tags is associative array of "tag name (lowercase)" => process
 * NULL - standard process
 * TRUE - no process
 * callback($contentOfTag) -> compressed content
 */
class HTMLMinifier
{
    /**
     * The constructor
     *
     * @param string $content
     * @param array $tags [optional]
     */
    public function __construct($content, array $tags = null)
    {
        $this->origin = $content;
        $this->tags = self::getDefaultsTags($tags);
    }

    /**
     * Compress
     *
     * @return string
     */
    public function run()
    {
        if ($this->compressed === null) {
            $this->process();
        }
        return $this->compressed;
    }

    /**
     * Returns the original content
     *
     * @return string
     */
    public function getOriginContent()
    {
        return $this->origin;
    }

    /**
     * Returns the compressed content
     *
     * @return string
     */
    public function getCompressedContent()
    {
        return $this->run();
    }

    /**
     * Returns the tags array
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Compress a HTML content
     *
     * @param string $content
     * @param array $tags [optional]
     * @return string
     */
    public static function compress($content, array $tags = null)
    {
        $minifier = new self($content, $tags);
        return $minifier->run();
    }

    /**
     * Compress the content of a file
     * (without checking the existence of the file and permissions)
     *
     * @param string $filename
     * @param array $tags [optional]
     * @return string
     */
    public static function compressFromFile($filename, array $tags = null)
    {
        return self::compress(file_get_contents($filename), $tags);
    }

    /**
     * Compress a file and save to another file
     * (without checking the existence of the files and permissions)
     *
     * @param string $source
     * @param string $destination
     * @param array $tags [optional]
     * @return string
     */
    public static function compressFile($source, $destination, array $tags = null)
    {
        $result = self::compressFromFile($source, $tags);
        file_put_contents($destination, $result);
        return $result;
    }

    /**
     * @param array $tags [optional]
     * @return array
     */
    public static function getDefaultsTags(array $tags = null)
    {
        if (!$tags) {
            $tags = self::$defaultsTags;
        } else {
            $tags = array_merge(self::$defaultsTags, $tags);
        }
        return $tags;
    }

    /**
     * Process of compress
     */
    private function process()
    {
        $result = [];
        $tag = null;
        $content = null;
        foreach (explode("\n", $this->origin) as $line) {
            if ($tag) {
                $r = Parser::findClose($line, $tag);
                if ($r->tag === null) {
                    $content[] = $line;
                    continue;
                }
                $content[] = $r->before;
                $result[] = Parser::process($content, $this->tags[$tag]);
                $tag = null;
                $content = null;
                $line = $r->after;
            }
            $line = ltrim($line);
            $r = Parser::parseLine($line, $this->tags);
            if ($r->tag === null) {
                $line = rtrim($line);
                if ($line !== '') {
                    $result[] = $line."\n";
                }
            } else {
                $result[] = $r->before;
                $tag = $r->tag;
                $content = [$r->after];
            }
        }
        $this->compressed = rtrim(implode($result));
    }

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $compressed;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private static $defaultsTags = [
        'pre' => true,
        'textarea' => true,
    ];
}
