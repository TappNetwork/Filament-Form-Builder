<?php

namespace Tapp\FilamentFormBuilder\Support;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Field labels are plain text or rich text (HTML from the rich label editor).
 *
 * Plain labels render exactly as before. Labels with HTML tags are sanitized, so scripts,
 * event handlers and javascript: links never reach the page, and block content (paragraphs,
 * headings, lists) is wrapped in Filament's prose styles so it reads as formatted text.
 */
final class FieldLabel
{
    private const string TAG_PATTERN = '/<\/?[a-z][a-z0-9]*(\s[^>]*)?\/?>/i';

    private const string BLOCK_TAG_PATTERN = '/<(p|h[1-6]|ul|ol|li|blockquote|hr|pre|div|table)[\s>\/]/i';

    public static function isRich(?string $label): bool
    {
        return $label !== null && preg_match(self::TAG_PATTERN, $label) === 1;
    }

    public static function toHtml(string $label): HtmlString
    {
        if (! str_contains($label, '<')) {
            return new HtmlString($label);
        }

        $html = self::sanitize($label);

        if (preg_match(self::BLOCK_TAG_PATTERN, $html) === 1) {
            $html = '<div class="fi-prose">'.$html.'</div>';
        }

        return new HtmlString($html);
    }

    /**
     * The label as text, for entries, exports and tables. Plain labels come back unchanged.
     */
    public static function toPlainText(string $label): string
    {
        if (! self::isRich($label)) {
            return $label;
        }

        $text = preg_replace('/<(br|\/p|\/h[1-6]|\/li|\/blockquote|\/div|\/tr)\s*\/?>/i', "$0\n", $label) ?? $label;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\s*\n\s*/', "\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * What the rich label editor starts with: the saved HTML, or a plain label split into paragraphs.
     */
    public static function toEditorHtml(?string $label): ?string
    {
        if (blank($label)) {
            return null;
        }

        if (self::isRich($label)) {
            return self::sanitize($label);
        }

        $paragraphs = preg_split('/\R\s*\R/', trim($label)) ?: [];

        return collect($paragraphs)
            ->map(fn (string $paragraph): string => '<p>'.preg_replace('/\R/', '<br>', e(trim($paragraph))).'</p>')
            ->implode('');
    }

    /**
     * Folds the rich label editor's fields back into the `label` column before a field is saved.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function fromEditorData(array $data): array
    {
        if (! empty($data['rich_label']) && is_string($data['rich_label_content'] ?? null)) {
            $data['label'] = self::sanitize($data['rich_label_content']);
        }

        unset($data['rich_label'], $data['rich_label_content']);

        return $data;
    }

    public static function sanitize(string $html): string
    {
        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        static $sanitizer = null;

        return $sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowRelativeLinks()
                ->dropElement('img')
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(100000),
        );
    }
}
