<?php

namespace App\Transformers\Loan;

use App\Models\Loan;
use App\Transformers\Payment\PaymentTransformer;
use App\Transformers\User\UserProfileTransformer;
use App\Transformers\User\UserTransformer;
use League\Fractal\TransformerAbstract;

class LoanTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'payments', 'userProfile'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Loan $loan)
    {
        return [
            'id' => $loan->id,
            'principal' => $loan->principal,
            'installment' => $loan->installment,
            'loan_start_date' => $loan->loan_start_date,
            'loan_end_date' => $loan->loan_end_date,
            'period' => $loan->period,
            'interest' => $loan->interest,
            'status' => $loan->status
        ];
    }

    public function includePayments(Loan $loan)
    {
        $payments = $loan->payments;

        return $this->collection($payments, new PaymentTransformer());
    }

    public function includeUserProfile(Loan $loan)
    {
        $user = $loan->user;

        return $this->item($user, new UserProfileTransformer());
    }
}
