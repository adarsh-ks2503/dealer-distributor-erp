<?php

namespace App\Models;

use Carbon\Carbon;
// changes by raza
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\OrderAllocation;
use App\Models\OrderAttachment;
use Illuminate\Support\Facades\Log;
// end changes by raza

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $casts = [
        'order_date' => 'date',
        'approval_time' => 'datetime',
    ];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'placed_by_dealer_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'placed_by_distributor_id');
    }

    // // changes by raza
    // public function allocations()
    // {
    //     return $this->hasMany(OrderAllocation::class);
    // }

    // public function attachments()
    // {
    //     return $this->hasMany(OrderAttachment::class);
    // }

    // end changes by raza

    // public static function generateOrderNumber(){
    //     try {
    //         $today = Carbon::today();

    //         // Determine financial year (starts April 1st)
    //         $year = $today->month >= 4 ? $today->year : $today->year - 1;
    //         $nextYear = substr($year + 1, -2); // Get last two digits of next year
    //         $financialYear = "{$year}-{$nextYear}";

    //         // Define financial year start and end dates
    //         $yearStart = Carbon::create($year, 4, 1)->startOfDay();
    //         $yearEnd = Carbon::create($year + 1, 3, 31)->endOfDay();

    //         // Get the last order for the current financial year
    //         $lastOrder = self::whereBetween('created_at', [$yearStart, $yearEnd])
    //             ->orderBy('id', 'desc')
    //             ->first();

    //         if ($lastOrder) {
    //             // Extract the counter from last order number
    //             $lastCounter = (int)substr($lastOrder->order_number, -4);
    //             $counter = $lastCounter + 1;
    //         } else {
    //             $counter = 1; // First order for the financial year
    //         }

    //         // Pad counter to 4 digits
    //         $counterPadded = str_pad($counter, 4, '0', STR_PAD_LEFT);

    //         return "ODR_{$financialYear}_{$counterPadded}";
    //     } catch (\Exception $e) {
    //         \Log::error('Error generating order number: ' . $e->getMessage());
    //         throw new \Exception('Failed to generate order number');
    //     }
    // }
    public static function generateOrderNumber(Carbon $date = null): string
    {
        try {
            // Agar date di gayi hai to woh istemaal karein, warna aaj ki date lein
            $carbonDate = $date ?? Carbon::now();

            // Financial year nirdhaarit karein (April 1st se shuru)
            $year = $carbonDate->month >= 4 ? $carbonDate->year : $carbonDate->year - 1;
            $nextYear = substr($year + 1, -2); // Agle saal ke aakhri do anko ko lein
            $financialYear = "{$year}-{$nextYear}";

            // Financial year ki shuruaat aur ant
            $yearStart = Carbon::create($year, 4, 1)->startOfDay();
            $yearEnd = Carbon::create($year + 1, 3, 31)->endOfDay();

            // Is financial year mein aakhri order dhoondhein
            $lastOrder = self::whereBetween('order_date', [$yearStart, $yearEnd])
                ->orderBy('id', 'desc')
                ->first();

            $counter = 1;
            if ($lastOrder && $lastOrder->order_number) {
                // Aakhri order number se counter nikaalein aur 1 jod dein
                $lastCounter = (int) substr($lastOrder->order_number, -4);
                $counter = $lastCounter + 1;
            }

            // Counter ko 4 digit ka banane ke liye '0' lagayein
            $counterPadded = str_pad($counter, 4, '0', STR_PAD_LEFT);

            return "ODR_{$financialYear}_{$counterPadded}";
        } catch (\Exception $e) {
            Log::error('Order number generate karne mein error: ' . $e->getMessage());
            throw new \Exception('Order number generate nahi ho saka.');
        }
    }

    public function allocations()
    {
        return $this->hasMany(OrderAllocation::class, 'order_id');
    }

    public function attachments()
    {
        return $this->hasMany(OrderAttachment::class, 'order_id');
    }

    public function placedByDealer()
    {
        return $this->belongsTo(Dealer::class, 'placed_by_dealer_id');
    }

    public function placedByDistributor()
    {
        return $this->belongsTo(Distributor::class, 'placed_by_distributor_id');
    }
}
