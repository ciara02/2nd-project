<?php

namespace App\Http\Controllers;

use App\Models\LDAPController;
use Illuminate\Http\Request;

class FetchEngineers extends Controller
{
  public function index()
{
    // Fetch users from LDAP
    $users = LDAPController::fetchUserFromLDAP() ?? [];

    // Return only the fields we need for the dropdown
    $engineers = array_map(function($user) {
        return [
            'name' => $user['engineer'],           // full name
            'email' => $user['email'],
            'samaccountname' => $user['samaccountname'], // lowercase key
        ];
    }, $users);

    return response()->json($engineers);
}


}
