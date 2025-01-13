<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StateCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(database_path('data/pin_codes.json'));
        $data = json_decode($json, true);

        foreach ($data as $pinCode) {
            DB::table('cities')->insert([
                'state_id' => $pinCode['district_id'],
                'name' => $pinCode['name'],
                'pin_code' => $pinCode['pin_code'],
            ]);
        }
    }
}
