<?php

namespace App\Http\Controllers;
use App\Models\Pool;
use DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function createClosingOpening()
    {
        $pool = Pool::where("type", "=", "Opening")        
        ->select('created_at')
        ->orderBy('id', 'desc')
        ->first();

        $create_closing_opening = Carbon::now();
                   
        $create_closing_opening->day = 0;
        $create_closing_opening->hour = 23;
        $create_closing_opening->minute = 59;
        // $create_closing_opening->minute -= 1;
        $create_closing_opening->second = 59;

        // return $create_closing_opening;

        if($pool->created_at != $create_closing_opening)
        {
            $closing = new Pool;

            $closing->type = "Closing";

            $closing->amount = $this->calculate();

            $closing->created_at = $create_closing_opening;

            $closing->save();


            $opening = new Pool;

            $opening->type = "Opening";

            $opening->amount = $this->calculate();

            $opening->created_at = $create_closing_opening;

            $opening->save();
        }

        // return [
        //     'carbon_now' => Carbon::now(), 
        //     'db__create' => $create_closing_opening] ;

            // return [
            //     'carbon_now' => Carbon::now(), 
            //     'db__create' => Carbon::now()->subMinutes(Carbon::now()->minute % 5)] ;
    }

    public function calculate()
    {
        $latest_balance = 0;

        $pools = Pool::select(
                'type',
                'amount',
                DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y %H:%i:%s") as date_time')
            )->get();

        foreach($pools as $pool)
        {
            if($pool->type == "Add")
            {
                $latest_balance += $pool->amount;
                
                $pool->balance = $latest_balance;
            }

            else if($pool->type == "Withdraw")
            {
                $latest_balance -= $pool->amount;
                
                $pool->balance = $latest_balance;
            }
        }

        
        return $latest_balance;

    }

    public function all()
    {

        $latest_balance = 0;

        $pools = Pool::select(
                'type',
                'amount',
                DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y %H:%i:%s") as date_time')
            )->get();

        foreach($pools as $pool)
        {
            if($pool->type == "Add")
            {
                $latest_balance += $pool->amount;
                
                $pool->balance = $latest_balance;
            }

            if($pool->type == "Withdraw")
            {
                $latest_balance -= $pool->amount;
                
                $pool->balance = $latest_balance;
            }

            if($pool->type == "Opening")
            {                
                $pool->balance = $latest_balance;
            }

            if($pool->type == "Closing")
            {
                $pool->balance = $latest_balance;
            }
        }

        

        return response(["Pool" => $pools], 200);

    }

    public function add(Request $request)
    {
        $this->createClosingOpening();

        $pool_last = Pool::orderBy('id', 'desc')->first();

        $add = new Pool;

        $add->type = "Add";

        $add->amount = $request->amount;

        $add->save();

        $add->balance = $this->calculate();
        
        return response([
            'type' => $add->type,
            'amount' => $add->amount,
            'balance' => $add->balance,
            'date_time' => DATE_FORMAT($add->created_at, "m/d/Y H:i:s")
        ], 200);
    }

    public function withdraw(Request $request)
    {
        $this->createClosingOpening();
        
        $pool_last = Pool::orderBy('id', 'desc')->first();

        $withdraw = new Pool;

        $withdraw->type = "Withdraw";

        $withdraw->amount = $request->amount;

        $withdraw->save();

        $withdraw->balance = $this->calculate();
        
        return response([
            'type' => $withdraw->type,
            'amount' => $withdraw->amount,
            'balance' => $withdraw->balance,
            'date_time' => DATE_FORMAT($withdraw->created_at, "m/d/Y H:i:s")
        ], 200);
    }
}
