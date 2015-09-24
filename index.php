<?php
/**
 * Compress HTML
 *
 * @package axy\min\html
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/min=html/master/LICENSE MIT
 * @link https://github.com/axypro/min-html repository
 * @uses PHP5.4+
 */

namespace axy\min\html;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
