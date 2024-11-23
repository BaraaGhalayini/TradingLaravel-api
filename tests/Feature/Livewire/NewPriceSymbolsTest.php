<?php

namespace Tests\Feature\Livewire;

use App\Livewire\NewPriceSymbols;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class NewPriceSymbolsTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(NewPriceSymbols::class)
            ->assertStatus(200);
    }
}
