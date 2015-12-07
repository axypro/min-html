<?php
/**
 * @package axy\min\html
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\min\html\helpers;

/**
 * The result of Parser::parseLine
 */
class ParseLineResult
{
    /**
     * The line content before of $tag content (or the whole line if $tag is empty)
     *
     * @var string
     */
    public $before;

    /**
     * The line content after of $tag content (or empty if $tag is empty)
     *
     * @var string
     */
    public $after;

    /**
     * An unclosed tag from the tags list
     *
     * @var string|null
     */
    public $tag;

    /**
     * The constructor
     *
     * @param string $before [optional]
     * @param string $tag [optional]
     * @param string $after [optional]
     */
    public function __construct($before = null, $tag = null, $after = null)
    {
        $this->before = $before;
        $this->tag = $tag;
        $this->after = $after;
    }

    /**
     * Appends to the before content
     *
     * @param string $content
     */
    public function append($content)
    {
        $this->before .= $content;
    }
}
