<?php

namespace App\Http\Controllers\API;

use Midtrans\Config;
use Midtrans\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback()
    {
        //set configurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');;
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        //buat instance midtrans notification
        $notification = new Notification();

        //Assign ke variabel untuk memudahkan coding
        $status     =   $notification->transaction_status;
        $type       =   $notification->payment_type;
        $fraud      =   $notification->fraud_status;
        $order_id   =   $notification->order_id;

        //Get Transaction id
        $order = explode('-', $order_id);

        //cari transaction berdasarkan id
        $transaction = Transaction::findOrFail($order[1]);

        //Handle notification status midtrans
        if($status == 'capture'){
            if($type == 'credit_card'){
                if($fraud == 'challeng'){
                    $transaction->status = 'PENDING';
                } else {
                    $transaction->status = 'SUCCESS';
                }
            }
        }
        else if($status == 'settlement'){
            $transaction->status = 'SUCCESS';
        }
        else if($status == 'pending'){
            $transaction->status = 'PENDING';
        }
        else if($status == 'deny'){
            $transaction->status = 'PENDING';
        }
        else if($status == 'expire'){
            $transaction->status = 'CHANCELLED';
        }
        else if($status == 'cencel'){
            $transaction->status = 'CHANCELLED';
        }

        //Simpan transaksi
        $transaction->save();

        //Return response untuk midtrans
        return response()->json([
            'meta'  => [
                    'code'      =>200,
                    'message'   => 'Midtrans Notification Success'
                    ]
        ]);
    }
}
