<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens, HasRoles, SoftDeletes;

    const STATUS = ['pending' => 0, 'active' => 1, 'blocked' => 2, 'spammer' => 3, 'suspend' => 4, 'review' => 5];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class,'user_id','id');
    }

    public function userVerifications()
    {
        return $this->hasMany(UserVerification::class);
    }

    public static function getPermissionNames($data): array
    {
        return $data->pluck('name')->toArray();
    }


    public static function revokeToken($user)
    {
        $tokens = $user->tokens->where('revoked', false);
        foreach ($tokens as $token) {
            $token->revoke();
        }
    }
}
