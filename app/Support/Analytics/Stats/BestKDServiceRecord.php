<?php

declare(strict_types=1);

namespace App\Support\Analytics\Stats;

use App\Enums\AnalyticKey;
use App\Enums\Mode;
use App\Models\Analytic;
use App\Support\Analytics\AnalyticInterface;
use App\Support\Analytics\BasePlayerStat;
use App\Support\Analytics\Traits\HasExportUrlGeneration;
use App\Support\Analytics\Traits\HasServiceRecordExport;
use Illuminate\Database\Eloquent\Collection;

class BestKDServiceRecord extends BasePlayerStat implements AnalyticInterface
{
    use HasServiceRecordExport;
    use HasExportUrlGeneration;

    public function title(): string
    {
        return 'Best KD (1k game min)';
    }

    public function key(): string
    {
        return AnalyticKey::BEST_KD_SR->value;
    }

    public function unit(): string
    {
        return ' KD';
    }

    public function property(): string
    {
        return 'kd';
    }

    public function displayProperty(Analytic $analytic): string
    {
        return number_format($analytic->value, 2);
    }

    public function results(int $limit = 10): ?Collection
    {
        return $this->builder()
            ->select('service_records.*')
            ->with(['player'])
            ->leftJoin('players', 'players.id', '=', 'service_records.player_id')
            ->where('is_cheater', false)
            ->where('mode', Mode::MATCHMADE_PVP)
            ->whereNull('season_number')
            ->where('total_matches', '>=', 1000)
            ->orderByDesc($this->property())
            ->limit($limit)
            ->get();
    }
}
