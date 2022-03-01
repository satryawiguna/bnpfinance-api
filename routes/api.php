<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/register/member', 'Api\V1\Auth\RegisterController@actionRegister')->name('api.register');
Route::post('/password/email', 'Api\V1\Auth\ForgotPasswordController@actionSendResetLinkEmail')->name('api.reset');
Route::get('email/verify/{id}', 'Api\V1\Auth\VerificationApiController@verify')->name('api.verification.verify');
Route::get('email/resend', 'Api\V1\Auth\VerificationApiController@resend')->name('api.verification.resend');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'Api\V1\Auth\AuthController@actionLogin')->name('api.login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/me', 'Api\V1\Auth\AuthController@actionMe')->name('api.me')
            ->middleware('verified');
        Route::post('/logout', 'Api\V1\Auth\AuthController@actionLogout')->name('api.logout');
    });
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/me', 'Api\V1\ProfileController@actionProfile')->name('api.profile');
        Route::put('/me/update', 'Api\V1\ProfileController@actionProfileUpdate')->name('api.profile_update');
        Route::group(['prefix' => 'me'], function () {
            Route::put('/password/update', 'Api\V1\ProfileController@actionPasswordUpdate')->name('api.password_update');
            Route::post('/photo/update', 'Api\V1\ProfileController@actionPhotoUpdate')->name('api.photo_update');
        });
    });

    Route::get('/users', 'Api\V1\UserController@actionUsers')->name('api.users');
    Route::post('/usersListSearch', 'Api\V1\UserController@actionUsersListSearch')->name('api.usersListSearch');
    Route::post('/usersPageSearch', 'Api\V1\UserController@actionUsersPageSearch')->name('api.usersPageSearch');
    Route::get('/user/{id}', 'Api\V1\UserController@actionUser')->name('api.user');
    Route::post('/user/store', 'Api\V1\UserController@actionUserStore')->name('api.user_store');
    Route::prefix('user')->group(function () {
        Route::put('/{id}/update', 'Api\V1\UserController@actionUserUpdate')->name('api.user_update');
        Route::delete('/{id}/delete', 'Api\V1\UserController@actionUserDelete')->name('api.user_delete');
        Route::get('/{id}/loans', 'Api\V1\UserController@actionUserLoans')->name('api.user_loans');
    });

    Route::get('/loans', 'Api\V1\LoanController@actionLoans')->name('api.loans');
    Route::get('/loan/{id}', 'Api\V1\LoanController@actionLoan')->name('api.loan');
    Route::post('/loan/store', 'Api\V1\LoanController@actionLoanStore')->name('api.loan_store');
    Route::put('/loan/update', 'Api\V1\LoanController@actionLoanUpdate')->name('api.loan_update');
    Route::prefix('loan')->group(function () {
        Route::delete('/{id}/delete', 'Api\V1\LoanController@actionLoanDelete')->name('api.loan_delete');
        Route::put('/{id}/updateToPaid', 'Api\V1\LoanController@actionLoanUpdateToPaid')->name('api.loan_update_to_paid');
        Route::get('/{id}/payments', 'Api\V1\LoanController@actionLoanPayments')->name('api.loan_payments');
    });


    Route::get('/payments', 'Api\V1\PaymentController@actionPayments')->name('api.payments');
    Route::get('/payment/{id}', 'Api\V1\PaymentController@actionPayment')->name('api.payment');
    Route::post('/payment/store', 'Api\V1\PaymentController@actionPaymentStore')->name('api.payment_store');
    Route::put('/payment/update', 'Api\V1\PaymentController@actionPaymentUpdate')->name('api.payment_update');
    Route::prefix('payment')->group(function () {
        Route::delete('/{id}/delete', 'Api\V1\PaymentController@actionPaymentDelete')->name('api.payment_delete');
    });

});
