<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::query()->create([
            'parent_id' => 0,
            'title' => '配饰',
            'logo' => '',
            'order' => 0,
        ]);

        Category::query()->create([
            'parent_id' => 1,
            'title' => '帽子',
            'logo' => '',
            'order' => 1,
        ]);


        Category::query()->create([
            'parent_id' => 2,
            'title' => '眼镜',
            'logo' => '',
            'order' => 2,
        ]);
    }
}
