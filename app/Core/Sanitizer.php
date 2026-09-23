<?php

namespace App\Core;

/**
 * Allowlist HTML sanitizer for admin-authored rich text (news body, job description).
 * Strips dangerous tags/attributes while preserving normal formatting output from the
 * rich text editor (headings, lists, links, images, alignment).
 */
class Sanitizer
{
    private const DANGEROUS_TAGS = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'svg', 'math', 'link', 'meta', 'base'];

    private const ALLOWED_ATTRS = ['href', 'src', 'alt', 'title', 'width', 'height', 'target', 'rel', 'class'];

    public static function html(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="__root__">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementById('__root__');
        if (!$root) {
            return strip_tags($html);
        }

        self::cleanNode($root, $doc);

        $result = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $result .= $doc->saveHTML($child);
        }

        return $result;
    }

    private static function cleanNode(\DOMElement $node, \DOMDocument $doc): void
    {
        $children = iterator_to_array($node->childNodes);

        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                /** @var \DOMElement $child */
                $tag = strtolower($child->tagName);

                if (in_array($tag, self::DANGEROUS_TAGS, true)) {
                    $node->removeChild($child);
                    continue;
                }

                self::cleanAttributes($child);
                self::cleanNode($child, $doc);
            }
        }
    }

    private static function cleanAttributes(\DOMElement $el): void
    {
        $attrs = iterator_to_array($el->attributes ?? []);

        foreach ($attrs as $attr) {
            $name = strtolower($attr->name);

            if (str_starts_with($name, 'on') || !in_array($name, self::ALLOWED_ATTRS, true)) {
                $el->removeAttribute($attr->name);
                continue;
            }

            if ($name === 'href' || $name === 'src') {
                $value = trim($attr->value);
                if (preg_match('#^\s*(javascript|data|vbscript):#i', $value)) {
                    $el->removeAttribute($attr->name);
                    continue;
                }
            }

            if ($name === 'class') {
                $allowedClasses = array_filter(
                    explode(' ', $attr->value),
                    fn ($c) => preg_match('/^ql-align-(left|center|right|justify)$/', $c)
                );
                if ($allowedClasses) {
                    $el->setAttribute('class', implode(' ', $allowedClasses));
                } else {
                    $el->removeAttribute('class');
                }
            }
        }

        if (strtolower($el->tagName) === 'a' && $el->getAttribute('target') === '_blank') {
            $el->setAttribute('rel', 'noopener noreferrer');
        }
    }
}
