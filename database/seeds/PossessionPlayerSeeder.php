<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PossessionPlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = glob(storage_path('app/bigdataball')."/*.csv");

        foreach($files as $file) {
            $teams = explode("@", substr(substr($file, -11), 0, 7));
            $visitorTeam = DB::table('teams')->where('short_name', $teams[0])->first();
            $homeTeam = DB::table('teams')->where('short_name', $teams[1])->first();

            $csv = Reader::createFromPath($file)->setHeaderOffset(0);
            $i = 0;
            foreach($csv as $record) {
                // Create the game based on first record
                if(!$i) {
                    // Season
                    $seasonName = $record['data_set'];
                    $season = DB::table('seasons')->where('name', $seasonName)->first();
                    $seasonId = $season ? $season->id : $seasonId = DB::table('seasons')->insertGetId([
                        'name' => $seasonName,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // Game
                    $gameId = DB::table('games')->insertGetId([
                        'game_date' => Carbon::parse($record['date'])->format('Y-m-d'),
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $visitorTeam->id,
                        'season_id' => $seasonId,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }

                $team = !empty($record['team']) ? DB::table('teams')->where('short_name', $record['team'])->first() : null;
                $possessionArgs = [
                    'game_id' => $gameId,
                    'team_id' => $team ? $team->id : null,
                    'period' => $record['period'],
                    'home_team_score' => $record['home_score'],
                    'away_team_score' => $record['away_score'],
                    'remaining_time' => strpos($record['remaining_time'], '-') === false ? Carbon::parse($record['remaining_time'])->format('H:i:s') : '00:00:00',
                    'elapsed' => strpos($record['elapsed'], '-') === false ? Carbon::parse($record['elapsed'])->format('H:i:s') : '00:00:00',
                    'play_length' => strpos($record['play_length'], '-') === false ? Carbon::parse($record['play_length'])->format('H:i:s') : '00:00:00',
                    'event_type' => $record['event_type'],
                    'type' => $record['type'],
                    'result' => $record['result'],
                    'points' => intval($record['points']) ? intval($record['points']) : null,
                    'num' => intval($record['num']) ? intval($record['num']) : null,
                    'outof' => intval($record['outof']) ? intval($record['outof']) : null,
                    'shot_distance' => floatval($record['shot_distance']) ? floatval($record['shot_distance']) : null,
                    'original_x' => floatval($record['original_x']) ? floatval($record['original_x']) : null,
                    'original_y' => floatval($record['original_y']) ? floatval($record['original_y']) : null,
                    'converted_x' => floatval($record['converted_x']) ? floatval($record['converted_x']) : null,
                    'converted_y' => floatval($record['converted_y']) ? floatval($record['converted_y']) : null,
                ];

                // Map players of different stats to id of player in our db
                $playerFields = [
                    'player' => 'player_id',
                    'away' => 'away_player_id',
                    'home' => 'home_player_id',
                    'assist' => 'assist_player_id',
                    'block' => 'block_player_id',
                    'entered' => 'entered_player_id',
                    'left' => 'left_player_id',
                    'opponent' => 'opponent_player_id',
                    'steal' => 'steal_player_id',
                    'possession' => 'possession_player_id',
                ];
                foreach($playerFields as $key => $value) {
                    if(!empty($record[$key])) {
                        $thisPlayerName = utf8_encode($record[$key]);
                        $thisPlayer = DB::table('players')->where('name', $thisPlayerName)->first();
                        $possessionArgs[$value] = $thisPlayer ? $thisPlayer->id : DB::table('players')->insertGetId([
                            'name' => $thisPlayerName,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                        // Update the latest team for the player
                        if($team && $possessionArgs[$value]) {
                            DB::update('update players set team_id=? where id=?', [
                                $team->id,
                                $possessionArgs[$value]
                            ]);
                        }
                    }
                }

                $possessionId = DB::table('possessions')->insertGetId($possessionArgs);

                // Visiting team players on court
                for($i=1; $i<=5; $i++) {
                    $thisPlayerName = utf8_encode($record['a'.$i]);
                    $thisPlayer = DB::table('players')->where('name', $thisPlayerName)->first();
                    $ppArgs = [
                        'possession_id' => $possessionId,
                        'team_id' => $visitorTeam->id,
                    ];
                    $ppArgs['player_id'] = $thisPlayer ? $thisPlayer->id : DB::table('players')->insertGetId([
                        'name' => $thisPlayerName,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                    DB::table('player_possession')->insert($ppArgs);
                }

                // Visiting team players on court
                for($i=1; $i<=5; $i++) {
                    $thisPlayerName = utf8_encode($record['h'.$i]);
                    $thisPlayer = DB::table('players')->where('name', $thisPlayerName)->first();
                    $ppArgs = [
                        'possession_id' => $possessionId,
                        'team_id' => $homeTeam->id,
                    ];
                    $ppArgs['player_id'] = $thisPlayer ? $thisPlayer->id : DB::table('players')->insertGetId([
                        'name' => $thisPlayerName,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                    DB::table('player_possession')->insert($ppArgs);
                }

                $i++;
            }
        }
    }
}
