<?php

use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = DB::table('divisions')->get();
        foreach($divisions as $d) {
            $client = new Client();
            $response = $client->request('GET', 'https://api-nba-v1.p.rapidapi.com/teams/divName/' . $d->name, [
                'headers' => [
                    'x-rapidapi-host' => 'api-nba-v1.p.rapidapi.com',
                	'x-rapidapi-key' => env('RAPID_API_KEY'),
                ],
            ]);
            $responseJSON = json_decode($response->getBody());
            $teams = $responseJSON->api->teams;

            foreach($teams as $t) {
                DB::table('teams')->insert([
                    'id' => $t->teamId,
                    'full_name' => $t->fullName,
                    'city' => $t->city,
                    'nickname' => $t->nickname,
                    'logo' => $t->logo,
                    'short_name' => $t->shortName,
                    'division_id' => $d->id,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
