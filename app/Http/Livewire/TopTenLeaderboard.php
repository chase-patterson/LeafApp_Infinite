<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\AnalyticKey;
use App\Models\Analytic;
use Illuminate\View\View;
use Livewire\Component;

class TopTenLeaderboard extends Component
{
    public string $analyticKey;

    public function render(): View
    {
        $topTen = Analytic::query()
            ->with(['player', 'game', 'map'])
            ->where('key', $this->analyticKey)
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        $analyticEnumKey = AnalyticKey::tryFrom($this->analyticKey);

        return view('livewire.top-ten-leaderboard', [
            'results' => $topTen,
            'analyticClass' => Analytic::getStatFromEnum($analyticEnumKey),
        ]);
    }
}
