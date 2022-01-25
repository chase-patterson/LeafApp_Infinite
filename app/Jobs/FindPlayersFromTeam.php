<?php
declare(strict_types = 1);

namespace App\Jobs;

use App\Models\Team;
use App\Services\Autocode\InfiniteInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class FindPlayersFromTeam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Team $team;
    private ?InfiniteInterface $client;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->team->faceit_id))->dontRelease()
        ];
    }

    public function handle(): void
    {
        $this->client = App::make(InfiniteInterface::class);

        foreach ($this->team->faceitPlayers->whereNull('player_id') as $teamPlayer) {
            $player = $this->client->appearance($teamPlayer->faceit_name);
            if ($player) {
                $teamPlayer->player()->associate($player);
                $teamPlayer->saveOrFail();
            }
        }
    }
}