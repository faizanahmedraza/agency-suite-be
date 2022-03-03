<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\V1\ProfileRequest;
use App\Http\Businesses\V1\UserBusiness;
use App\Http\Requests\V1\ChangePasswordRequest;
use App\Http\Resources\SuccessResponse;
/* Response */
use App\Http\Resources\V1\UserResponse;



/* Helper */
use Illuminate\Support\Facades\DB;

/**
 * @group User Management
 */

class UserController extends Controller
{
    /**
     * Change user password
     *
     * @authenticated
     *
     * @headerParam Authorization string required
     *
     * @responseFile 200 app/Http/Requests/V1/ChangePasswordRequest.php
     *
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        UserBusiness::changePassword($request);
        return (new SuccessResponse([]));
    }
}
