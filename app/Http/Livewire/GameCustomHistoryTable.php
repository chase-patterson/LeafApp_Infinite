<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Player;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GameCustomHistoryTable extends Component
{
    use WithPagination;

    public Player $player;

    public bool $isScrimEditor = false;

    public array $scrimGameIds = [];

    // @phpstan-ignore-next-line
    public $listeners = [
        '$refresh',
        'toggleScrimMode',
    ];

    public function toggleScrimMode(): void
    {
        $this->isScrimEditor = ! $this->isScrimEditor;
    }

    /** @codeCoverageIgnore */
    public function updatedScrimGameIds(): void
    {
        $this->emitTo(ScrimTogglePanel::class, 'syncGameIds', $this->scrimGameIds);
    }

    public function paginationView(): string
    {
        return 'pagination::bulma';
    }

    public function render(): View
    {
        return view('livewire.game-custom-history-table', [
            'isScrimEditor' => $this->isScrimEditor,
            'games' => $this->player
                ->games()
                ->with(['map', 'category'])
                ->where(function ($query) {
                    $query
                        ->where('is_lan', '=', false)
                        ->orWhereNull('is_lan');
                })
                ->whereDoesntHave('playlist')
                ->orderByDesc('occurred_at')
                ->paginate(16),
        ]);
    }
}
