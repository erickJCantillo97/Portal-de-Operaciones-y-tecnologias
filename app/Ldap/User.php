<?php

namespace App\Ldap;

use LdapRecord\Models\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [];


}
