<?php

use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $westId = DB::table('conferences')->insertGetId([
            'name' => 'West',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $westDivisions = ['Northwest', 'Pacific', 'Southwest'];
        foreach($westDivisions as $d) {
            DB::table('divisions')->insert([
                'conference_id' => $westId,
                'name' => $d,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

        $eastId = DB::table('conferences')->insertGetId([
            'name' => 'East',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $eastDivisions = ['Atlantic', 'Central', 'Southeast'];
        foreach($eastDivisions as $d) {
            DB::table('divisions')->insert([
                'conference_id' => $eastId,
                'name' => $d,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
