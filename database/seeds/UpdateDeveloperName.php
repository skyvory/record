<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Developer;

class UpdateDeveloperName extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $developers = DB::table('developers')->get();
        foreach($developers as $dev) {
            var_dump($dev);
            if(empty($dev->original) && !empty($dev->romaji)) {
                // $dev->original = $dev->romaji;
                // $dev->romaji = null;
                // echo "WOW";
                // $dev->save();

                $developer = Developer::find($dev->id);
                $developer->original = $developer->romaji;
                $developer->romaji = null;
                $developer->save();
                echo "OK ";
            }
        }

        Model::reguard();
    }
}
