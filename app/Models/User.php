<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use LdapRecord\Laravel\Auth\HasLdapUser;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasLdapUser, AuthenticatesWithLdap;

    protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    protected $appends = [
        'profile_photo_url',
        'short_name',
        'photo'
    ];


    public function getShortNameAttribute(){
        return explode(' ', $this->name)[0] .' '.explode(' ', $this->name)[count(explode(' ', $this->name)) - 2 > -1 ? count(explode(' ', $this->name)) - 2: 0];
    }

    public function getPhotoAttribute()
    {
        $value = $this->ldap->thumbnailphoto ?? null;
        // Due to LDAP's multi-valued nature, all values will be
        // contained inside of an array. We will attempt to
        // retrieve the first one, or supply a default.
        if(!isset($value)){
            return $this->profile_photo_url;
        }
        $data = $value[0] ;
        $image = base64_encode($data);

        $mime = 'image/jpeg';

        if (function_exists('finfo_open')) {
            $finfo = finfo_open();

            $mime = finfo_buffer($finfo, $data, FILEINFO_MIME_TYPE);

            return "data:$mime;base64,$image";
        }

        return "data:$mime;base64,$image";
    }

}
