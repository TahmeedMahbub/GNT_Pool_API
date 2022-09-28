<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pool;
use Carbon\Carbon;

class PoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pool = Pool::where('type', '=', 'Opening')
        ->where('amount', '=', 0.00)
        ->first();

        if($pool == null)
        {
            $pool = new Pool();

            $pool->type = "Opening";

            $pool->amount = 0.00;

            $pool->creaeted_at = Carbon::now();

            $pool->save();
        }
    }
}
