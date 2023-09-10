<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Profile extends Component
{
    public User $user;

    protected $rules = [
        'user.username' => 'required|string|max:255',
        'user.about' => 'max:140'
    ];

    public function mount(): void
    {
        $this->user = auth()->user();
    }

    public function save(): void
    {
        $this->validate();

        $this->user->save();

        $this->emitSelf('notify-saved');
    }

    public function render(): View
    {
        return view('livewire.profile')
            ->layout('components.layouts.app');
    }
}
