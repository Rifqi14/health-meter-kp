<?php

use App\Models\Template;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('templates')->truncate();
        $templates = json_decode(File::get(database_path('datas/templates.json')));
        foreach ($templates as $template) {
            Template::create([
                'code' => $template->code,
                'name' => $template->name,
                'description'   => $template->description
            ]);
        }
    }
}
