<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Showtime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class BookingPaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;


    public $booking;
    public $payment;

    public $room;
    public $showtime;


    public function __construct(Booking $booking, Payment $payment)
    {
        $this->booking = $booking;
        $this->payment = $payment;

        $this->showtime = Showtime::with('room')
            ->where('id', $this->booking->thongtinchieu_id)
            ->first();
        $this->room = $this->showtime ? $this->showtime->room : null;
    }

    public function build()
    {
        return $this->subject('Thanh toán thành công - Thông tin chi tiết ')
            ->view('emails.send_bill_payment_success')
            ->with([
                'booking' => $this->booking,
                'payment' => $this->payment,
                'room' => $this->room,
                'showtime' => $this->showtime,
            ]);
    }
}
