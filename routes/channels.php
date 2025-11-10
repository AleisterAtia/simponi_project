<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// HANYA JIKA ID order-nya tersimpan di session mereka.
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    // Kita akan simpan 'tracking_order_id' di session setelah checkout
    return (int) session('tracking_order_id') === (int) $orderId;
});
