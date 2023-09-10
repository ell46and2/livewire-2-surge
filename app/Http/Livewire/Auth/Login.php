<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $credentials = $this->validate();

        if (!auth()->attempt($credentials)) {
            $this->addError('email', trans('auth.failed'));
            return;
        }

        return redirect()->intended('/');
    }

    public function render(): View
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth');
    }
}
