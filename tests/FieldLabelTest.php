<?php

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Support\FieldLabel;

function labelledSchema(string $type, string $label): array
{
    $field = new FilamentFormField;
    $field->setRawAttributes([
        'id' => 1,
        'filament_form_id' => 1,
        'type' => $type,
        'label' => $label,
        'required' => false,
        'hint' => null,
        'rules' => null,
        'options' => null,
        'schema' => null,
        'step' => null,
        'order' => 1,
    ]);

    $form = new FilamentForm;
    $form->setRawAttributes([
        'id' => 1,
        'name' => 'Test Form',
        'description' => null,
        'redirect_url' => null,
        'permit_guest_entries' => false,
        'is_wizard' => false,
    ]);
    $form->setRelation('filamentFormFields', Collection::make([$field]));

    $show = new Show;
    $show->filamentForm = $form;

    return $show->getFormSchema();
}

dataset('plain labels', [
    'short' => 'First Name',
    'punctuation' => "Student's date of birth (MM/DD/YYYY)?",
    'ampersand' => 'Terms & Conditions',
    'entity' => 'Caf&eacute; name',
    'quotes' => 'Type "I agree" to continue',
    'multi-line' => "Please read and sign.\n\nI agree to the program rules.\nI will attend every session.",
    'long' => str_repeat('I understand the expectations of the program. ', 40),
    'unicode' => 'Nombre del estudiante — firma ✓',
]);

it('renders plain labels exactly as before', function (string $label) {
    // Before this change, Show rendered every label as new HtmlString($label).
    expect(FieldLabel::toHtml($label)->toHtml())->toBe((new HtmlString($label))->toHtml())
        ->and(FieldLabel::isRich($label))->toBeFalse()
        ->and(FieldLabel::toPlainText($label))->toBe($label);
})->with('plain labels');

it('renders plain labels exactly as before in the form schema', function (string $type, string $label) {
    $schema = labelledSchema($type, $label);

    expect($schema[0]->getLabel())->toBeInstanceOf(HtmlString::class)
        ->and($schema[0]->getLabel()->toHtml())->toBe($label);
})->with([
    'TEXT', 'TEXTAREA', 'SELECT', 'CHECKBOX', 'CHECKBOX_LIST', 'RADIO', 'TOGGLE', 'DATE_PICKER', 'RICH_EDITOR', 'HEADING',
])->with('plain labels');

it('keeps existing inline formatting in labels', function (string $label) {
    expect(FieldLabel::toHtml($label)->toHtml())->toBe($label);
})->with([
    '<strong>Required:</strong> parent signature',
    'Name <em>(as it appears on the ID)</em>',
    'Line one<br />Line two',
    '<b>Bold</b> and <i>italic</i> and <u>underlined</u>',
]);

it('strips scripts, event handlers and dangerous links from labels in the form schema', function (string $type, string $label, string $forbidden) {
    $html = labelledSchema($type, $label)[0]->getLabel()->toHtml();

    expect(strtolower($html))->not->toContain($forbidden);
})->with(['TEXT', 'CHECKBOX'])->with([
    'script tag' => ['Name<script>alert(1)</script>', '<script'],
    'script body' => ['Name<script>alert(1)</script>', 'alert(1)'],
    'img onerror' => ['<img src=x onerror="alert(1)"> Name', 'onerror'],
    'img tag' => ['<img src="https://example.com/x.png"> Name', '<img'],
    'svg onload' => ['<svg onload="alert(1)"></svg>Name', 'onload'],
    'javascript link' => ['<a href="javascript:alert(1)">Click</a>', 'javascript:'],
    'data link' => ['<a href="data:text/html;base64,PHNjcmlwdD4=">Click</a>', 'data:'],
    'iframe' => ['<iframe src="https://evil.test"></iframe>Name', '<iframe'],
    'style attribute' => ['<p style="position:fixed;inset:0">Name</p>', 'style='],
    'style tag' => ['<style>body{display:none}</style>Name', '<style'],
    'onclick' => ['<p onclick="alert(1)">Name</p>', 'onclick'],
    'form' => ['<form action="https://evil.test"><input name="x"></form>Name', '<form'],
]);

it('keeps safe links and makes them noopener', function () {
    $html = FieldLabel::toHtml('Read the <a href="https://example.org/rules" target="_blank">rules</a>')->toHtml();

    expect($html)->toContain('href="https://example.org/rules"')
        ->toContain('rel="noopener noreferrer"');
});

it('renders an agreement with headings and lists as prose', function () {
    $label = '<h2>STEPS Agreement</h2><p>Please read and sign.</p><ul><li>Attend every session</li><li>Complete <strong>all</strong> modules</li></ul><ol><li>One</li></ol><blockquote>Note</blockquote>';

    $html = FieldLabel::toHtml($label)->toHtml();

    expect($html)->toStartWith('<div class="fi-prose">')
        ->toContain('<h2>STEPS Agreement</h2>')
        ->toContain('<ul><li>Attend every session</li>')
        ->toContain('<strong>all</strong>')
        ->toContain('<ol><li>One</li></ol>')
        ->toContain('<blockquote>Note</blockquote>');
});

it('sanitizes rich labels in the form schema', function () {
    $schema = labelledSchema('CHECKBOX', '<h2>Agreement</h2><p>Sign below<script>alert(1)</script></p>');

    expect($schema[0]->getLabel()->toHtml())
        ->toBe('<div class="fi-prose"><h2>Agreement</h2><p>Sign below</p></div>');
});

it('shows rich labels as readable plain text', function () {
    expect(FieldLabel::toPlainText('<h2>Agreement</h2><p>Read &amp; sign</p><ul><li>One</li><li>Two</li></ul>'))
        ->toBe("Agreement\nRead & sign\nOne\nTwo")
        ->and(FieldLabel::toPlainText('<strong>Required:</strong> parent signature'))
        ->toBe('Required: parent signature');
});

it('starts the rich editor from the existing label', function () {
    expect(FieldLabel::toEditorHtml(null))->toBeNull()
        ->and(FieldLabel::toEditorHtml(''))->toBeNull()
        ->and(FieldLabel::toEditorHtml("Please read.\n\nLine one\nLine 2 & three"))
        ->toBe('<p>Please read.</p><p>Line one<br>Line 2 &amp; three</p>')
        ->and(FieldLabel::toEditorHtml('<p>Saved</p><script>x</script>'))
        ->toBe('<p>Saved</p>');
});

it('folds the rich editor content into the label before saving', function () {
    expect(FieldLabel::fromEditorData([
        'type' => 'CHECKBOX',
        'rich_label' => true,
        'rich_label_content' => '<p>Sign <a href="javascript:alert(1)">here</a></p>',
    ]))->toBe([
        'type' => 'CHECKBOX',
        'label' => '<p>Sign <a rel="noopener noreferrer">here</a></p>',
    ]);

    expect(FieldLabel::fromEditorData([
        'type' => 'TEXT',
        'rich_label' => false,
        'label' => 'First Name',
    ]))->toBe([
        'type' => 'TEXT',
        'label' => 'First Name',
    ]);
});

it('lists entry answers under plain-text questions without changing plain ones', function () {
    $entry = new FilamentFormUser;
    $entry->entry = [
        ['field' => 'First Name', 'answer' => 'Ada', 'type' => 'Text'],
        ['field' => '<h2>Agreement</h2><p>I agree</p>', 'answer' => true, 'type' => 'Checkbox'],
        ['field' => 'Colors', 'answer' => ['red', 'blue'], 'type' => 'Checkbox List'],
    ];

    expect($entry->key_value_entry)->toBe([
        'First Name' => 'Ada',
        "Agreement\nI agree" => true,
        'Colors' => '["red","blue"]',
    ]);
});
