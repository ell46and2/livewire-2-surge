<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|same:passwordConfirmation'
    ];

    public function updated(string $field)
    {
        if ($field === 'passwordConfirmation') {
            $field = 'password';
        }
        $this->validateOnly($field);
    }

    public function register(): Redirector
    {
        $data = $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);

        auth()->login($user);

        return redirect('/');
    }

    public function render(): View
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.auth');
    }
}
