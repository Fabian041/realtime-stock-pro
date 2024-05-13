<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\TtMaterial;
use App\Models\TmTransaction;
use Illuminate\Console\Command;

class ReceiveStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receive:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update stock based on uploaded excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the current date and time
        $currentDateTime = Carbon::now();

        // Fetch TtMaterial records where id_transaction is null and created_at is today
        $executeds = TtMaterial::whereNull('id_transaction')
            ->whereDate('created_at', $currentDateTime->format('Y-m-d'))
            ->get();

        // Iterate over the fetched records
        foreach ($executeds as $executed) {
            // Get the specified time from the current TtMaterial record
            $specifiedTime = Carbon::parse($executed->delivery_time)->format('H:i');

            // Get the current time in hours and minutes
            $currentTime = $currentDateTime->format('H:i');

            // Check if the current time matches the specified time
            if ($currentTime == $specifiedTime) {
                // Your logic here
                $transaction = TmTransaction::select('id')->where('name', 'STO')->first();

                // Update the current TtMaterial record
                $executed->update([
                    'id_transaction' =>  $transaction->id,
                ]);
            }
        }
    }
}
