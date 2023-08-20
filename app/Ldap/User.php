<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    protected $appends = ['photo'];
    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [];


}
