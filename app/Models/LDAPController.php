<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LDAPController extends Model
{

    public $fullName;
    public $email;
    public $samAccountName;

    /**
     * LDAPUser constructor.
     *
     * @param string $fullName
     * @param string $email
     * @param string $samAccountName
     */
    public function __construct($fullName = null, $email = null, $samAccountName = null)
    {
        parent::__construct();

        $this->fullName = $fullName;
        $this->email = $email;
        $this->samAccountName = $samAccountName;
    }

    /**
     * Fetch LDAP data and create LDAPUser instances.
     *
     * @return array An array of LDAPUser instances.
     */
    public static function fetchFromLDAP()
    {
        $ldapServer = '10.105.33.31';
        $ldapPort = 389;
        $ldapUsername = 'MSI\Administrator';
        $ldapPassword = 'msidcm@DMIN2019';
        $ldapBaseDn = 'dc=msi,dc=com';

        $ldapConn = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        ldap_bind($ldapConn, $ldapUsername, $ldapPassword);

        $searchFilter = '(&(objectClass=person)(objectCategory=Person)(sAMAccountName=*)(memberOf=CN=ASA System Engineers,CN=Users,DC=msi,DC=com))';

        $searchResults = ldap_search($ldapConn, $ldapBaseDn, $searchFilter);
        $entries = ldap_get_entries($ldapConn, $searchResults);

        ldap_close($ldapConn);

        $ldapUsers = [];
        foreach ($entries as $entry) {
            if (!empty($entry['samaccountname'][0])) {
                $email = !empty($entry['mail'][0]) ? strtolower(trim($entry['mail'][0])) : '';
                $sn = !empty($entry['sn'][0]) ? trim($entry['sn'][0]) : '';
                $givenName = !empty($entry['givenname'][0]) ? trim($entry['givenname'][0]) : '';

                $fullName = $givenName . ' ' . $sn;
                $samAccountName = $entry['samaccountname'][0];

                $ldapUsers[] = new self($fullName, $email, $samAccountName);
            }
        }

        return $ldapUsers;
    }

public static function fetchUserFromLDAP()
{
    $ldapServer = '10.105.33.31';
    $ldapPort = 389;
    $ldapUsername = "MSI\\Administrator";
    $ldapPassword = 'msidcm@DMIN2019';
    $ldapBaseDn = 'DC=msi,DC=com';

    // Connect
    $ldapConn = ldap_connect($ldapServer, $ldapPort);
    if (!$ldapConn) {
        return ['error' => 'Cannot connect to LDAP server'];
    }

    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

    // Bind
    if (!@ldap_bind($ldapConn, $ldapUsername, $ldapPassword)) {
        return ['error' => 'LDAP bind failed'];
    }

    // Multi-group filter
    $searchFilter = "(&(objectCategory=Person)(sAMAccountName=*)(|(memberOf=CN=ASA System Engineers,CN=Users,DC=msi,DC=com)(memberOf=CN=Network Team,CN=Users,DC=msi,DC=com)))";

    $attributes = ['givenname', 'sn', 'mail', 'samaccountname', 'memberof'];

    $result = ldap_search($ldapConn, $ldapBaseDn, $searchFilter, $attributes);

    $entries = ldap_get_entries($ldapConn, $result);

    ldap_unbind($ldapConn);

    $adUsers = [];

    for ($i = 0; $i < $entries['count']; $i++) {
        $entry = $entries[$i];

        $givenName = !empty($entry['givenname'][0]) ? trim($entry['givenname'][0]) : '';
        $sn = !empty($entry['sn'][0]) ? trim($entry['sn'][0]) : '';
        $email = !empty($entry['mail'][0]) ? strtolower(trim($entry['mail'][0])) : '';

        $adUsers[] = [
            'engineer' => $givenName . ' ' . $sn,
            'email' => $email,
            'samaccountname' => $entry['samaccountname'][0] ?? ''
        ];
    }

    // Sort alphabetically by engineer name
    usort($adUsers, function($a, $b) {
        return strcmp($a['engineer'], $b['engineer']);
    });

    return $adUsers;
}


    public static function tagEngineer($filter)
    {
        $ldapServer = '10.105.33.31';
        $ldapPort = 389;
        $ldapUsername = 'MSI\Administrator';
        $ldapPassword = 'msidcm@DMIN2019';
        $ldapBaseDn = 'dc=msi,dc=com';
    
        $ldapConn = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
    
        ldap_bind($ldapConn, $ldapUsername, $ldapPassword);
    
        $searchResults = ldap_search($ldapConn, $ldapBaseDn, $filter);
        $entries = ldap_get_entries($ldapConn, $searchResults);
    
        ldap_close($ldapConn);
    
        $ldapUsers = [];
        foreach ($entries as $entry) {
            if (!empty($entry['samaccountname'][0])) {
                $email = !empty($entry['mail'][0]) ? strtolower(trim($entry['mail'][0])) : '';
                $sn = !empty($entry['sn'][0]) ? trim($entry['sn'][0]) : '';
                $givenName = !empty($entry['givenname'][0]) ? trim($entry['givenname'][0]) : '';
    
                $fullName = $givenName . ' ' . $sn;
                $samAccountName = $entry['samaccountname'][0];
    
                // Ensure LDAPEngineer constructor is correctly accepting parameters
                $ldapUsers[] = new self($fullName, $email, $samAccountName);
            }
        }
    
        return $ldapUsers;
    }
    

    public static function SupervisorLDAP()
    {
        $ldapServer = '10.105.33.31';
        $ldapPort = 389;
        $ldapUsername = 'MSI\Administrator';
        $ldapPassword = 'msidcm@DMIN2019';
        $ldapBaseDn = 'dc=msi,dc=com';

        $ldapConn = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        ldap_bind($ldapConn, $ldapUsername, $ldapPassword);

        // $searchFilter = '(&(objectClass=person)(objectCategory=Person)(sAMAccountName=*)(memberOf=CN=ASA System Engineers,CN=Users,DC=msi,DC=com))';
        $searchFilter = '(&(objectCategory=Person)(sAMAccountName=*)(memberOf=CN=ASA System Leaders,CN=Users,DC=msi,DC=com))';

        $searchResults = ldap_search($ldapConn, $ldapBaseDn, $searchFilter);
        $entries = ldap_get_entries($ldapConn, $searchResults);

        ldap_close($ldapConn);

        $ldapUsers = [];
        foreach ($entries as $entry) {
            if (!empty($entry['samaccountname'][0])) {
                $email = !empty($entry['mail'][0]) ? strtolower(trim($entry['mail'][0])) : '';
                $sn = !empty($entry['sn'][0]) ? trim($entry['sn'][0]) : '';
                $givenName = !empty($entry['givenname'][0]) ? trim($entry['givenname'][0]) : '';

                $fullName = $givenName . ' ' . $sn;
                $samAccountName = $entry['samaccountname'][0];

                $ldapUsers[] = new self($fullName, $email, $samAccountName);
            }
        }

        return $ldapUsers;
    }
}


