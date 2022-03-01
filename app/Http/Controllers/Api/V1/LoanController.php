<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Transformers\Loan\LoanTransformer;
use App\Transformers\Payment\PaymentTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class LoanController extends Controller
{
    public function actionLoans()
    {
        if (!Auth::user()->can('view_by_admin', [Loan::class])) {
            return $this->responseUnauthorized();
        }

        $loans = Loan::all();

        if (!$loans)
            return $this->responseUnprocessable(new MessageBag(['Loan is empty']));

        return fractal($loans, new LoanTransformer())
            ->includeUserProfile()
            ->toArray();
    }

    public function actionLoan($id)
    {
        if (!Auth::user()->can('detail', [Loan::class, $id])) {
            return $this->responseUnauthorized();
        }

        $loan = Loan::find($id);

        if (!$loan)
            return $this->responseUnprocessable(new MessageBag(['Loan is not found']));

        return fractal($loan, new LoanTransformer())
            ->includeUserProfile()
            ->toArray();
    }

    public function actionLoanStore(Request $request)
    {
        if (!Auth::user()->can('create', [Loan::class])) {
            return $this->responseUnauthorized();
        }

        // Register request validation
        $validatedLoanStore = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'principal' => 'required',
            'installment' => 'required',
            'loan_start_date' => 'required|date',
            'loan_end_date' => 'required|date',
            'period' => 'required|integer',
            'interest' => 'required',
        ]);

        if ($validatedLoanStore->fails()) {
            return $this->responseUnprocessable($validatedLoanStore->errors());
        }

        try {
            // Check loan of user with status debt
            $loan = (new Loan())->where([
                ['user_id', '=', $request->input('user_id')],
                ['status', '=', 'debt'],
            ])->get();

            if ($loan->count() > 0)
                return $this->responseUnprocessable(new MessageBag(['There loan status debt', $loan[0]->id]));

            // Store into loan
            $loan = new Loan([
                'user_id' => $request->input('user_id'),
                'principal' => $request->input('principal'),
                'installment' => $request->input('installment'),
                'loan_start_date' => date("Y-m-d", strtotime($request->input('loan_start_date'))),
                'loan_end_date' => date("Y-m-d", strtotime($request->input('loan_end_date'))),
                'period' => $request->input('period'),
                'interest' => $request->input('interest')
            ]);

            $loan->save();

            return fractal($loan, new LoanTransformer)
                ->toArray();
        } catch (Exception $e) {
            return $this->responseServerError($e->getMessage());
        }
    }

    public function actionLoanUpdate(Request $request)
    {
        if (!Auth::user()->can('update', [Loan::class])) {
            return $this->responseUnauthorized();
        }

        $validatedLoanUpdate = Validator::make($request->all(), [
            'principal' => 'required',
            'installment' => 'required',
            'loan_start_date' => 'required|date',
            'loan_end_date' => 'required|date',
            'period' => 'required|integer',
            'interest' => 'required',
        ]);

        if ($validatedLoanUpdate->fails()) {
            return $this->responseUnprocessable($validatedLoanUpdate->errors());
        }

        $loan = Loan::find($request->input('id'));

        if (!$loan)
            return $this->responseUnprocessable(new MessageBag(['Loan is not found']));

        try {
            $loan->update([
                "principal" => $request->input('principal') ?: $loan->principal,
                "installment" => $request->input('installment') ?: $loan->installment,
                "loan_start_date" => $request->input('loan_start_date') ?: $loan->loan_start_date,
                "loan_end_date" => $request->input('loan_end_date') ?: $loan->loan_end_date,
                "period" => $request->input('period') ?: $loan->period,
                "interest" => $request->input('interest') ?: $loan->interest,
            ]);

            return fractal($loan, new LoanTransformer())
                ->toArray();

        } catch (Exception $e) {
            return $this->responseServerError($e->getMessage());
        }
    }

    public function actionLoanUpdateToPaid($id) {
        $loan = Loan::find($id);

        $loan->status = "paid";
        $loan->save();

        return response()->json(['message' => 'Loan was paid'], 200);
    }

    public function actionLoanDelete($id)
    {
        if (!Auth::user()->can('delete', [Loan::class])) {
            return $this->responseUnauthorized();
        }

        $loan = Loan::find($id);

        if (!$loan)
            return $this->responseUnprocessable(new MessageBag(['Loan is not found']));

        $loan->delete();

        return $this->responseSuccess('Loan deleted');
    }

    public function actionLoanPayments($id)
    {
        if (!Auth::user()->can('view', [Loan::class, $id])) {
            return $this->responseUnauthorized();
        }

        $payment = Payment::where('loan_id', '=', $id)->get();

        return fractal($payment, new PaymentTransformer())
            ->includeLoan()
            ->toArray();
    }
}
