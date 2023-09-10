<?php

use App\Models\User;
use function Pest\Laravel\get;

test('registration pages contains livewire component', function () {
    get('register')->assertSeeLivewire('auth.register');
});

test('can register', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@doe.com')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertRedirect('/');
    $this->assertTrue(User::whereEmail('jane@doe.com')->exists());
    $this->assertEquals('jane@doe.com', auth()->user()->email);
});

test('email is required', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', '')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertHasErrors(['email' => 'required']);
});

test('email is a valid email', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'not-valid-email')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertHasErrors(['email' => 'email']);
});

test('email has not been taken already', function () {
    User::factory()->create(['email' => 'jane@doe.com']);

    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@doe.com')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);
});

test('see email has been taken already validation error on typing', function () {
    User::factory()->create(['email' => 'jane@doe.com']);

    Livewire::test('auth.register')
        ->set('email', 'john@doe.com')
        ->assertHasNoErrors()
        ->set('email', 'jane@doe.com')
        ->assertHasErrors(['email' => 'unique']);
});

test('password is required', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@doe.com')
        ->set('password', '')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertHasErrors(['password' => 'required']);
});

test('password is a minimum of 6 characters', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@doe.com')
        ->set('password', '12345')
        ->set('passwordConfirmation', '12345')
        ->call('register')
        ->assertHasErrors(['password' => 'min']);
});

test('password matches password confirmation', function () {
    Livewire::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@doe.com')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'not-secret')
        ->call('register')
        ->assertHasErrors(['password' => 'same']);
});
