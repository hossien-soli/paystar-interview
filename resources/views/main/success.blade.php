@extends('layouts.master')

@section('content')
<div class="alert alert-success">
    <i class="mdi mdi-check-circle-outline me-1"></i> پرداخت موفق آمیز بود و سفارش شما با موفقیت ثبت شد.
</div>

<h5>
    شماره کارت : 
    {{ $order->card_number }}
</h5>

<h5>
    مبلغ پرداخت شده : 
    {{ $order->payment_amount }} ریال
</h5>

<h5>
    شناسه تراکنش : 
    {{ $order->transaction_id }}
</h5>

<h5>
    کد پیگیری : 
    {{ $order->tracking_code }}
</h5>
@endsection
