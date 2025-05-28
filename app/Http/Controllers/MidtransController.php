<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Transaction as CoreTransaction;

class MidtransController extends Controller
{
    public function process(Request $request)
    {
        $orderId = 'ORDER-' . rand(100000, 999999);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $request->amount,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        Transaction::create([
            'order_id' => $orderId,
            'name' => $request->name,
            'email' => $request->email,
            'amount' => $request->amount,
        ]);

        return response()->json(['snap_token' => $snapToken]);
    }

    public function callback(Request $request)
    {
        $notification = new Notification();

        $transaction = Transaction::where('order_id', $notification->order_id)->first();
        if ($transaction) {
            $transaction->update([
                'transaction_status' => $notification->transaction_status,
                'payment_type' => $notification->payment_type,
                'transaction_id' => $notification->transaction_id,
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function chargeViaCoreApi(Request $request)
    {
        $orderId = 'ORDER-' . rand(100000, 999999);

        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$request->amount,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
            'bank_transfer' => [
                'bank' => 'bca'
            ]
        ];

        $charge = CoreTransaction::charge($params);

        Transaction::create([
            'order_id' => $orderId,
            'name' => $request->name,
            'email' => $request->email,
            'amount' => $request->amount,
            'transaction_status' => $charge->transaction_status ?? 'pending',
            'payment_type' => $charge->payment_type ?? 'bank_transfer',
            'transaction_id' => $charge->transaction_id ?? null,
        ]);

        return response()->json(['message' => 'Transaksi berhasil dibuat.', 'data' => $charge]);
    }
}
