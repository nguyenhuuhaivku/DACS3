<?php


namespace App\Mail;


use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class PaymentRejectionNotification extends Mailable
{
    use Queueable, SerializesModels;


    public $payment;
    public $reservation;


    public function __construct(Payment $payment, Reservation $reservation)
    {
        $this->payment = $payment;
        $this->reservation = $reservation;
    }


    public function build()
    {
        return $this->subject('Thông báo từ chối thanh toán - ' . $this->payment->PaymentCode)
            ->view('emails.payment-rejected');
    }
}
