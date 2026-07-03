<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

beforeEach(function (): void {
    Schema::create('users', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });

    $migration = require dirname(__DIR__).'/database/migrations/create_dynamic_filament_form_tables.php.stub';
    $migration->up();
});

function createFormBuilderTestUser(string $email): User
{
    $user = new User;
    $user->forceFill([
        'name' => 'Test User',
        'email' => $email,
        'password' => bcrypt('password'),
    ]);
    $user->save();

    return $user;
}

it('updates the existing entry by default for authenticated users', function (): void {
    $user = createFormBuilderTestUser('default@example.com');

    $form = FilamentForm::query()->create([
        'name' => 'Feedback',
        'permit_guest_entries' => false,
        'locked' => false,
    ]);

    $this->actingAs($user);

    $show = new Show;
    $show->filamentForm = $form;
    $show->allowMultipleSubmissions = false;

    $method = new ReflectionMethod(Show::class, 'persistFormEntry');
    $method->invoke($show, [['field' => 'rating', 'answer' => 'Agree']]);
    $method->invoke($show, [['field' => 'rating', 'answer' => 'Disagree']]);

    expect(FilamentFormUser::query()->count())->toBe(1)
        ->and(FilamentFormUser::query()->first()->entry[0]['answer'])->toBe('Disagree');
});

it('creates a new entry on each submission when allowMultipleSubmissions is enabled', function (): void {
    $user = createFormBuilderTestUser('multi@example.com');

    $form = FilamentForm::query()->create([
        'name' => 'Feedback',
        'permit_guest_entries' => false,
        'locked' => false,
    ]);

    $this->actingAs($user);

    $show = new Show;
    $show->filamentForm = $form;
    $show->allowMultipleSubmissions = true;

    $method = new ReflectionMethod(Show::class, 'persistFormEntry');
    $method->invoke($show, [['field' => 'rating', 'answer' => 'Agree']]);
    $method->invoke($show, [['field' => 'rating', 'answer' => 'Disagree']]);

    expect(FilamentFormUser::query()->count())->toBe(2)
        ->and(FilamentFormUser::query()->orderBy('id')->pluck('entry')->map(fn (array $entry) => $entry[0]['answer'])->all())
        ->toBe(['Agree', 'Disagree']);
});
