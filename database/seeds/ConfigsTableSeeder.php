<?php

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->truncate();
        $configs = json_decode(File::get(database_path('datas/configs.json')));
        foreach ($configs as $config) {
            Config::create([
                'option'=> $config->option,
                'value'=> $config->value
            ]);
        }
    }
}
