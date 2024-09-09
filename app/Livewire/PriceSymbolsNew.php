<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PriceSymbolsNew extends Component
{

    #[Layout('dashboard-live')]
    public function render(): View|Application|Factory
    {
        return view('livewire.price-symbols-new');

    }
}
