<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Payment;
use App\Transformers\Payment\PaymentTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class PaymentController extends Controller
{
    public function actionPayments()
    {
        if (!Auth::user()->can('view_by_admin', [Payment::class])) {
            return $this->responseUnauthorized();
        }

        $payments = Payment::with(['loan' => function($query) {
            return $query->with(['user'])->get();
        }])->get();

        if (!$payments)
            return $this->responseUnprocessable(new MessageBag(['Loan is empty']));

        return fractal($payments, new PaymentTransformer())
            ->toArray();
    }

    public function actionPayment($id)
    {
        if (!Auth::user()->can('view', [Payment::class])) {
            return $this->responseUnauthorized();
        }

        $payment = Payment::find($id);

        if (!$payment)
            return $this->responseUnprocessable(new MessageBag(['Payment is not found']));

        return fractal($payment, new PaymentTransformer())
            ->toArray();
    }

    public function actionPaymentStore(Request $request)
    {
        if (!Auth::user()->can('create', [Payment::class])) {
            return $this->responseUnauthorized();
        }

        // Register request validation
        $validatedPaymentStore = Validator::make($request->all(), [
            'loan_id' => 'required|integer',
            'amount' => 'required|integer',
            'balance' => 'required|integer',
            'payment_date' => 'required|date'
        ]);

        if ($validatedPaymentStore->fails()) {
            return $this->responseUnprocessable($validatedPaymentStore->errors());
        }

        try {
            // Store into payment
            $payment = new Payment([
                'loan_id' => $request->input('loan_id'),
                'amount' => $request->input('amount'),
                'balance' => $request->input('balance'),
                'payment_date' => $request->input('payment_date'),
                'description' => $request->input('description')
            ]);
            $payment->save();

            return fractal($payment, new PaymentTransformer)
                ->toArray();
        } catch (Exception $e) {
            return $this->responseServerError($e->getMessage());
        }
    }

    public function actionPaymentUpdate(Request $request)
    {
        if (!Auth::user()->can('update', [Payment::class])) {
            return $this->responseUnauthorized();
        }

        $validatedPaymentUpdate = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'balance' => 'required|integer',
            'payment_date' => 'required|date',
        ]);

        if ($validatedPaymentUpdate->fails()) {
            return $this->responseUnprocessable($validatedPaymentUpdate->errors());
        }

        $payment = Payment::find($request->input('id'));

        if (!$payment)
            return $this->responseUnprocessable(new MessageBag(['Payment is not found']));

        try {
            $payment->update([
                "amount" => $request->input('amount') ?: $payment->amount,
                "balance" => $request->input('balance') ?: $payment->balance,
                "payment_date" => $request->input('payment_date') ?: $payment->payment_date,
                "description" => $request->input('description') ?: $payment->description
            ]);

            return fractal($payment, new PaymentTransformer())
                ->toArray();

        } catch (Exception $e) {
            return $this->responseServerError($e->getMessage());
        }
    }

    public function actionPaymentDelete($id)
    {
        if (!Auth::user()->can('delete', [Payment::class])) {
            return $this->responseUnauthorized();
        }

        $payment = Payment::find($id);

        if (!$payment)
            return $this->responseUnprocessable(new MessageBag(['Payment is not found']));

        $payment->delete();

        return $this->responseSuccess('Payment deleted');
    }
}
