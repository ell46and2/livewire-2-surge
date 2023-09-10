<?php

use App\Models\User;
use function Pest\Laravel\get;

test('can see livewire profile component on profile page', function () {
    loginAsUser();

    get('/profile')
        ->assertSuccessful();
});

test('can update profile', function () {
    $user = loginAsUser();

    Livewire::test('profile')
        ->set('user.username', 'JD')
        ->set('user.about', 'some text')
        ->call('save');

    $user->refresh();

    $this->assertEquals('JD', $user->username);
    $this->assertEquals('some text', $user->about);
});

test('profile info is pre populated', function () {
    $user = User::factory()->create([
        'username' => 'foo',
        'about' => 'bar'
    ]);
    loginAsUser($user);

    Livewire::test('profile')
        ->assertSet('user.username', 'foo')
        ->assertSet('user.about', 'bar');
});

test('message is shown on save', function () {
    $user = User::factory()->create([
        'username' => 'foo',
        'about' => 'bar'
    ]);
    loginAsUser($user);

    Livewire::test('profile')
        ->assertDontSee('profile saved!')
        ->call('save')
        ->assertSee('profile saved!');
});

test('name is required', function () {
    $user = loginAsUser();

    Livewire::test('profile')
        ->set('user.username', '')
        ->set('user.about', 'some text')
        ->call('save')
        ->assertHasErrors(['user.username' => 'required']);
});

test('name must be less than or equal to 255 characters', function () {
    $user = loginAsUser();

    Livewire::test('profile')
        ->set('user.username', str_repeat('a', 256))
        ->set('user.about', 'some text')
        ->call('save')
        ->assertHasErrors(['user.username' => 'max']);
});

test('about must be less than or equal to 140 characters', function () {
    $user = loginAsUser();

    Livewire::test('profile')
        ->set('user.username', 'JD')
        ->set('user.about', str_repeat('a', 141))
        ->call('save')
        ->assertHasErrors(['user.about' => 'max']);
});
