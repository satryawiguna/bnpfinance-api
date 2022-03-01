<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view_by_admin(User $user)
    {
        return $user->role_id === 1;
    }

    public function view(User $user, $paymentId)
    {
        $payment = Payment::find($paymentId);
        $loan = Loan::find($payment->id);

        return $user->role_id == 1 || $user->id == $loan->user_id;
    }

    public function detail(User $user, $paymentId)
    {
        $payment = Payment::find($paymentId);
        $loan = Loan::find($payment->id);

        return $user->role_id == 1 || $user->id == $loan->user_id;
    }

    public function create(User $user)
    {
        return $user->role_id === 1;
    }

    public function update(User $user)
    {
        return $user->role_id === 1;
    }

    public function delete(User $user)
    {
        return $user->role_id === 1;
    }
}
