<?php
/**
 * @package axy\min\html
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\min\html\helpers;

class Parser
{
    /**
     * Parse of HTML line
     *
     * @param string $line
     * @param array $tags [optional]
     * @return \axy\min\html\helpers\ParseLineResult
     */
    public static function parseLine($line, array $tags = null)
    {
        $result = new ParseLineResult('');
        while ($line !== '') {
            if (!preg_match('~^(?<before>.*?)\<(?<tag>/?[a-z0-9-]+)(?<attr>.*?)?>(?<after>.*?)$~is', $line, $matches)) {
                $result->append($line);
                break;
            }
            $tag = $matches['tag'];
            $after = $matches['after'];
            $tagL = strtolower($matches['tag']);
            $result->append($matches['before'].'<'.$tag.$matches['attr'].'>');
            if (isset($tags[$tagL])) {
                $pattern = '~^(?<before>.*?)(?<tag></'.preg_quote($tagL).'>)(?<after>.*?)$~is';
                if (!preg_match($pattern, $after, $matches)) {
                    $result->tag = $tagL;
                    $result->after = $after;
                    break;
                }
                $content = $matches['before'];
                $content = self::process($content, $tags[$tagL]);
                $result->append($content.$matches['tag']);
                $line = $matches['after'];
            } else {
                $line = $after;
            }
        }
        if (!$result->tag) {
            $result->before = rtrim($result->before);
        }
        return $result;
    }

    /**
     * Finds a closed tag in the line
     *
     * @param string $line
     * @param string $tag
     * @return \axy\min\html\helpers\ParseLineResult
     */
    public static function findClose($line, $tag)
    {
        $pos = stripos($line, '</'.$tag.'>');
        if ($pos === false) {
            return new ParseLineResult($line);
        }
        return new ParseLineResult(substr($line, 0, $pos), $tag, substr($line, $pos));
    }

    /**
     * @param string|array $content
     * @param callable|bool $cb
     * @return string
     */
    public static function process($content, $cb)
    {
        if (is_array($content)) {
            $content = implode("\n", $content);
        }
        if ($cb !== true) {
            $content = call_user_func($cb, $content);
        }
        return $content;
    }
}
