<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseSuccess($message = 'Success.')
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
        ], 200);
    }

    public function responseResourceUpdated($message = 'Resource updated.')
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
        ], 200);
    }

    public function responseResourceCreated($message = 'Resource created.')
    {
        return response()->json([
            'status' => 201,
            'message' => $message,
        ], 201);
    }

    public function responseResourceDeleted($message = 'Resource deleted.')
    {
        return response()->json([
            'status' => 204,
            'message' => $message,
        ], 204);
    }

    public function responseUnauthorized($errors = ['Unauthorized.'])
    {
        return response()->json([
            'status' => 401,
            'errors' => $errors,
        ], 401);
    }

    public function responseUnprocessable($errors)
    {
        return response()->json([
            'status' => 422,
            'errors' => $errors,
        ], 422);
    }

    public function responseServerError($errors = ['Server error.'])
    {
        return response()->json([
            'status' => 500,
            'errors' => $errors
        ], 500);
    }
}
