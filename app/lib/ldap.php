<?php

namespace ldap;

abstract class AuthStatus
{
    const FAIL = "Authentication failed";
    const OK = "Authentication OK";
    const SERVER_FAIL = "Unable to connect to LDAP server";
    const ANONYMOUS = "Anonymous log on";
}

// The LDAP server
class LDAP
{
    private $server = "127.0.0.1";
    private $domain = "localhost";
    private $admin = "admin";
    private $password = "";

    public function __construct($server, $domain, $admin = "", $password = "")
    {
        $this->server = $server;
        $this->domain = $domain;
        $this->admin = $admin;
        $this->password = $password;
    }

    // Authenticate the against server the domain\username and password combination.
    public function authenticate($user)
    {
        $user->auth_status = AuthStatus::FAIL;

        $ldap = ldap_connect($this->server) or $user->auth_status = AuthStatus::SERVER_FAIL;
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = ldap_bind($ldap, $user->username."@".$this->domain, $user->password);

        if($ldapbind)
        {
            if(empty($user->password))
            {
                $user->auth_status = AuthStatus::ANONYMOUS;
            }
            else
            {
                $result = $user->auth_status = AuthStatus::OK;

                $this->_get_user_info($ldap, $user);
            }
        }
        else
        {
            $result = $user->auth_status = AuthStatus::FAIL;
        }

        ldap_close($ldap);
    }

    // Get an array of users or return false on error
    public function get_users($filter)
    {   $filter = "(sAMAccountname=$filter)";
        $ldap = ldap_connect($this->server) or die("ldap.connect.error");

        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 10000);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = ldap_bind($ldap, $this->admin."@".$this->domain, $this->password);

        $dc = explode(".", $this->domain);
        $base_dn = "";
        foreach($dc as $_dc) $base_dn .= "dc=".$_dc.",";
        $base_dn = substr($base_dn, 0, -1);
        $sr=ldap_search($ldap, $base_dn, "(&".$filter."(objectClass=user)(objectCategory=person)(|(mail=*)(telephonenumber=*))(!(userAccountControl:1.2.840.113556.1.4.803:=2)))", array("cn", "dn", "memberof", "mail"));
        $info = ldap_get_entries($ldap, $sr);

        for($i = 0; $i < $info["count"]; $i++)
        {
            $users[$i]["name"] = $info[$i]["cn"][0];
            $users[$i]["mail"] = $info[$i]["mail"][0];
        }

        return $users;
    }

    private function _get_user_info($ldap, $user)
    {
        $dc = explode(".", $this->domain);

        $base_dn = "";
        foreach($dc as $_dc) $base_dn .= "dc=".$_dc.",";

        $base_dn = substr($base_dn, 0, -1);

        $sr=ldap_search($ldap, $base_dn, "(&(objectClass=user)(objectCategory=person)(samaccountname=".$user->username."))", array("cn", "dn", "memberof", "mail"));
        $info = ldap_get_entries($ldap, $sr);

        $user->groups = Array();
        for($i = 0; $i < $info[0]["memberof"]["count"]; $i++)
            array_push($user->groups, $info[0]["memberof"][$i]);

        $user->name = $info[0]["cn"][0];
        $user->dn = $info[0]["dn"];
        $user->mail = $info[0]["mail"][0];

        if(!is_array($user->other_telephone[$t])) $user->other_telephone[$t] = Array();
    }
}

class User
{
    var $auth_status = AuthStatus::FAIL;
    var $username = "Anonymous";
    var $password = "";

    var $groups = Array();
    var $dn = "";
    var $name = "";
    var $mail = "";


    public function __construct($username, $password)
    {       
        $this->auth_status = AuthStatus::FAIL;
        $this->username = $username;
        $this->password = $password;
    }

    public function get_auth_status()
    {
        return $this->auth_status;
    }
 }
?>
