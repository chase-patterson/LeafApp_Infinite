<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Player;
use App\Support\Session\ModeSession;
use App\Support\Session\SeasonSession;
use Illuminate\View\View;
use Livewire\Component;

class OverviewPage extends Component
{
    public Player $player;

    // @phpstan-ignore-next-line
    public $listeners = [
        '$refresh',
    ];

    public function render(): View
    {
        $mode = ModeSession::get();
        $serviceRecordType = $mode->toPlayerRelation();
        $season = SeasonSession::model();
        $isAllSeasons = $season->key === SeasonSession::$allSeasonKey;

        return view('livewire.overview-page', [
            'mode' => $mode,
            'isAllSeasons' => $isAllSeasons,
            'serviceRecord' => $this->player->$serviceRecordType()->ofSeason($season)->first(),
            'season' => $season,
        ]);
    }
}
