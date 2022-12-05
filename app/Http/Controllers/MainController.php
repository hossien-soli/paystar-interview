<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Throwable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public function home()
    {
        $title = "خانه";
        return view('main.home',compact('title'));
    }

    public function newOrder(Request $request)
    {
        $cardNumber = $request->input('card_number');
        $validate = $cardNumber && strlen($cardNumber) == 16;
        if (!$validate) { return $this->jsonMessage(false,'شماره کارت نامعتبر است!',null); }
        
        DB::beginTransaction();
        try {
            $orderAmount = 5000;
            $order = Order::query()->create([
                'card_number' => $cardNumber,
                'amount' => $orderAmount,
                'created_at' => now()->toDateTimeString(),
            ]);
            $orderId = $order->id;

            $paymentApiGatewayId = config('custom.paystar_gateway_id');
            $paymentApiHmacKey = config('custom.paystar_hmac_key');
            $validate = $paymentApiGatewayId && $paymentApiHmacKey;
            if (!$validate) { throw new Exception; }

            $paymentApiCallback = route('main.callback');
            $payload = $orderAmount . '#' . $orderId . '#' . $paymentApiCallback;
            $paymentApiSign = hash_hmac('sha512',$payload,$paymentApiHmacKey);

            $paymentApiData = [
                'amount' => $orderAmount,
                'order_id' => (string) $orderId,
                'callback' => $paymentApiCallback,
                'sign' => $paymentApiSign,
            ];

            $handle = curl_init('https://core.paystar.ir/api/pardakht/create');
            curl_setopt($handle,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($handle,CURLOPT_HTTPHEADER,['Content-Type: application/json','Authorization: Bearer ' . $paymentApiGatewayId]);
            curl_setopt($handle,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($handle,CURLOPT_POSTFIELDS,json_encode($paymentApiData));
            $paymentApiResponse = curl_exec($handle);
            curl_close($handle);

            $paymentApiResponse = $paymentApiResponse ? json_decode($paymentApiResponse,true) : null;
            if (!$paymentApiResponse) { throw new Exception; }
            
            $validate = isset($paymentApiResponse['status']) && $paymentApiResponse['status'] == 1 && isset($paymentApiResponse['data'])
                            && $paymentApiResponse['data'];
            if (!$validate) { throw new Exception; }

            $data = $paymentApiResponse['data'];
            $validate = isset($data['token']) && isset($data['ref_num']) && isset($data['payment_amount']);
            if (!$validate) { throw new Exception; }

            $token = $data['token'];
            $refNum = $data['ref_num'];
            $paymentAmount = $data['payment_amount'];
            
            $order->token = $token;
            $order->payment_amount = $paymentAmount;
            $order->ref_num = $refNum;
            $order->save();

            DB::commit();

            $redirectUrl = 'https://core.paystar.ir/api/pardakht/payment?token=' . $token;
            return $this->jsonMessage(true,null,[
                'redirect_url' => $redirectUrl,
            ]);
        }
        catch (Exception $ex) {
            DB::rollBack();
            return $this->jsonMessage(false,'مشکلی پیش آمد. لطفا دوباره تلاش کنید! در صورت تداوم مشکل با پشتیبانی تماس بگیرید.',null);
        }
        catch (Throwable $tr) {
            DB::rollBack();
            return $this->jsonMessage(false,'مشکلی پیش آمد. لطفا دوباره تلاش کنید! در صورت تداوم مشکل با پشتیبانی تماس بگیرید.',null);
        }
    }

    public function callback(Request $request)
    {
        $status = $request->input('status');
        $orderId = $request->input('order_id');
        $refNum = $request->input('ref_num');
        $transactionId = $request->input('transaction_id');
        $cardNumber = $request->input('card_number');
        $trackingCode = $request->input('tracking_code');

        $validate = $status && $orderId && $refNum && $transactionId && ctype_digit((string) $orderId);
        if (!$validate) {
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }

        $paymentSuccess = $status === 1 || $status === '1';
        if (!$paymentSuccess) {
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }

        $validate = $cardNumber && $trackingCode;
        if (!$validate) {
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }

        $order = Order::query()->where('id',$orderId)->where('ref_num',$refNum)->whereNot('paid',1)->first();
        if (!$order) {
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }

        if ($order->card_number != $cardNumber) {
            Session::flash('swalWarning','پرداخت با شماره کارتی که اعلام کردید انجام نشده. مبلغ کسر شده در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }

        DB::beginTransaction();
        try {
            $order->transaction_id = $transactionId;
            $order->tracking_code = $trackingCode;
            $order->paid = 1;
            $order->paid_at = now()->toDateTimeString();
            $order->save();

            $paymentApiGatewayId = config('custom.paystar_gateway_id');
            $paymentApiHmacKey = config('custom.paystar_hmac_key');
            $validate = $paymentApiGatewayId && $paymentApiHmacKey;
            if (!$validate) { throw new Exception; }

            $orderAmount = $order->amount;
            $payload = $orderAmount . '#' . $refNum . '#' . $cardNumber . '#' . $trackingCode;
            $paymentApiSign = hash_hmac('sha512',$payload,$paymentApiHmacKey);

            $paymentApiData = [
                'ref_num' => $refNum,
                'amount' => $orderAmount,
                'sign' => $paymentApiSign,
            ];

            $handle = curl_init('https://core.paystar.ir/api/pardakht/verify');
            curl_setopt($handle,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($handle,CURLOPT_HTTPHEADER,['Content-Type: application/json','Authorization: Bearer ' . $paymentApiGatewayId]);
            curl_setopt($handle,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($handle,CURLOPT_POSTFIELDS,json_encode($paymentApiData));
            $paymentApiResponse = curl_exec($handle);
            curl_close($handle);

            $paymentApiResponse = $paymentApiResponse ? json_decode($paymentApiResponse,true) : null;
            if (!$paymentApiResponse) { throw new Exception; }
            
            $validate = isset($paymentApiResponse['status']) && $paymentApiResponse['status'] == 1 && isset($paymentApiResponse['data'])
                                    && $paymentApiResponse['data'];
            if (!$validate) { throw new Exception; }

            DB::commit();

            return Redirect::to(route('main.success') . '?order_id=' . $orderId);
        }
        catch (Exception $ex) {
            DB::rollBack();
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }
        catch (Throwable $tr) {
            DB::rollBack();
            Session::flash('swalWarning','پرداخت ناموفق بود. ممکن است که توسط خودتان لغو شده باشد و یا اگر که مبلغی از حساب شما کسر شده است در کمتر از 48 ساعت دیگر به حساب شما بازمیگردد!');
            return Redirect::route('main.home');
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $validate = $orderId && ctype_digit($orderId);
        if (!$validate) {
            Session::flash('swalWarning','سفارش یافت نشد!');
            return Redirect::route('main.home');
        }

        $order = Order::query()->find($orderId);
        if (!$order) {
            Session::flash('swalWarning','سفارش یافت نشد!');
            return Redirect::route('main.home');
        }

        $title = "سفارش موفق";
        return view('main.success',compact('title','order'));
    }
}
