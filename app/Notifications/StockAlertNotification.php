<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StockAlertNotification extends Notification
{
    use Queueable;

    public $menu;

    public function __construct($menu)
    {
        $this->menu = $menu;
    }

    public function via($notifiable)
    {
        // Simpan ke database
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Stok Habis!',
            'message' => "Menu '{$this->menu->name}' telah habis terjual.",
            'menu_id' => $this->menu->id,
            'icon' => 'bi-x-circle-fill', // Ikon Bootstrap
            'color' => 'text-red-500', // Warna Tailwind
            'url' => route('admin.menu.index') // Link ke halaman stok
        ];
    }
}
