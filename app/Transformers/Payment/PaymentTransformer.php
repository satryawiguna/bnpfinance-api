<?php

namespace App\Transformers\Payment;

use App\Models\Payment;
use App\Transformers\Loan\LoanTransformer;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
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
        'loan'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Payment $payment)
    {
        return [
            'amount' => $payment->amount,
            'balance' => $payment->balance,
            'payment_date' => $payment->payment_date,
            'description' => $payment->description
        ];
    }

    public function includeLoan(Payment $payment)
    {
        $loan = $payment->loan;

        return $this->item($loan, new LoanTransformer());
    }
}
