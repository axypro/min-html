# axy\min\html

[![Latest Stable Version](https://img.shields.io/packagist/v/axy/min-html.svg?style=flat-square)](https://packagist.org/packages/axy/min-html)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/axypro/min-html/master.svg?style=flat-square)](https://travis-ci.org/axypro/min-html)

Compress HTML.

* GitHub: [axypro/min-html](https://github.com/axypro/min-html)
* Composer: [axy/min-html](https://packagist.org/packages/axy/min-html)

PHP 5.4+

Library does not require any dependencies (except composer packages).

## Compression

Simply removes the indentation at the beginning and end of lines.
With some exceptions:

* Indents inside some tags (as `<pre>` and `<textarea>`) are relevant.
* The content of some tags can be handled in a special way (compress for `<script>` and `<style>` for example).

## Example

The source content:

```html
<head>
    <title>Test</title>
    <script>
    var x = 2 + 2;
    console.log(x);
    </script>
</head>
<body>
    <h1>Test</h1>
    <p>
    This is
    example
    of HTML compression.
    </p>
    <pre>
    Content of PRE
    is PRE
    </pre>
</body>
```

The compressed content:

```html
<head>
<title>Test</title>
<script>var x=2+2;console.log(x);</script>
</head>
<body>
<h1>Test</h1>
<p>
This is
example
of HTML compression.
</p>
<pre>
    Content of PRE
    is PRE
</pre>
</body>
```

## API

The library defines the single public class `axy\min\html\HTMLMinifier`.

Methods:

* `__construct(string $content [, array $tags])`
* `run(void): string`
* `getOriginContent(void): string`
* `getCompressedContent(void): string`
* `getTags(void): array`

Static methods:

* `compress(string $content [, array $tags]): string`
* `compressFromFile(string $content [, array $tags]): string`
* `compressFile(string $source, string $destination [, array $tags]): string`
* `getDefaultsTags([array $tags]): array`

## Example

Using static:

```php
use axy\min\html\HTMLMinifier;

$source = 'index.html';
$destination = 'index.min.html';

HTMLMinifier::compressFile($source, $destination);
```

Without static:

```php
$min = new HTMLMinifier($content);
$min->run();
echo $min->getCompressedContent();
```

## Tags

The optional array `tags` specifies how to handle content of the tags.

Defaults is

```php
[
    'pre' => true,
    'textarea' => true,
]
```

Argument `$tags` merges with the defaults.

`TRUE` - do not change.
Or callback.

```php
$tags = [
    'script' => function ($content) {
        return JSMinify::minify($content);
    },
    'textarea' => null, // Remove rule for TEXTAREA
    'style' => true, // Content of STYLE does not change
];

HTMLMinifier::compressFile($source, $destination, $tags);
```

