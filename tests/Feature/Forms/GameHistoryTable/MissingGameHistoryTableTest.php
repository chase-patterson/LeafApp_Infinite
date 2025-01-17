<?php

declare(strict_types=1);

namespace Tests\Feature\Forms\GameHistoryTable;

use App\Http\Livewire\GameHistoryTable;
use App\Models\Player;
use Livewire\Livewire;
use Tests\TestCase;

class MissingGameHistoryTableTest extends TestCase
{
    public function testValidResponseFromHaloDotApi(): void
    {
        // Arrange
        $player = Player::factory()->createOne();

        // Act & Assert
        Livewire::test(GameHistoryTable::class, [
            'player' => $player,
        ])
            ->call('render')
            ->assertViewHas('games');
    }
}
