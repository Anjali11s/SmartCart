<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetExceededMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cartTotal;
    public $budgetAmount;

    public function __construct(User $user, $cartTotal, $budgetAmount)
    {
        $this->user = $user;
        $this->cartTotal = $cartTotal;
        $this->budgetAmount = $budgetAmount;
    }

    public function build()
    {
        return $this->subject('Budget Alert: SmartCart')
                    ->markdown('emails.budget-exceeded');
    }
}