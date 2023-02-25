<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Medal;
use App\Models\ServiceRecord;
use App\Support\Session\ModeSession;
use App\Support\Session\SeasonSession;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MedalsLeaderboard extends Component
{
    use WithPagination;

    public Medal $medal;

    // @phpstan-ignore-next-line
    public $listeners = [
        '$refresh',
    ];

    public function paginationSimpleView(): string
    {
        return 'pagination::bulma-simple';
    }

    public function render(): View
    {
        $modeSession = ModeSession::get();
        $seasonSession = SeasonSession::get();

        $query = ServiceRecord::query()
            ->with('player')
            ->leftJoin('players', 'players.id', '=', 'service_records.player_id')
            ->where('is_cheater', false)
            ->selectRaw('ROW_NUMBER() OVER(ORDER BY value DESC) AS place,
                CAST(JSON_EXTRACT(medals, "$.'.$this->medal->id.'") as unsigned) as value,
                mode, total_seconds_played, player_id')
            ->where('mode', $modeSession->value)
            ->whereRaw('CAST(JSON_EXTRACT(medals, "$.'.$this->medal->id.'") as unsigned) > 0')
            ->orderByRaw('value DESC, total_seconds_played DESC');

        if ($seasonSession === -1) {
            $query->whereNull('season_number');
        } else {
            $query->where('season_number', $seasonSession);
        }
        $results = $query->simplePaginate(15);

        return view('livewire.medals-leaderboard', [
            'results' => $results,
        ]);
    }
}
