<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use Jumbojett\OpenIDConnectClient;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
	 public static function getSecretKey(){
		return getenv('JWT_SECRET_KEY');
	} 

    public static function openIDConnect($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('openIDConnect');
        }

        // Configure with Keycloak details
        $oidc = new OpenIDConnectClient(
            getenv('KEYCLOAK_REALM_URL'),
            getenv('KEYCLOAK_CLIENT_ID'),
            getenv('KEYCLOAK_CLIENT_SECRET'),
        );

        $oidc->setRedirectURL(getenv('KEYCLOAK_REDIRECT_URL'));
        return $oidc;
    }
}
