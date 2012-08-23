<?php
error_reporting(E_ALL); // Debug
 
if(!require_once($wgSMFPathAPI))
        die('Could not load the SMF API!');

function AutoAuthenticateSMF($wgUser, &$result)
{
        global $user_info;
	//echo '<pre>';
	//print_r($wgUser);
	//echo '</pre>';	

        // wiki user need setting?
        if(!(isset($wgUser))){
                $wgUser = new User();
                $wgUser->newFromSession();
                $wgUser->load();
        }
 
        if($wgUser->IsAnon() && !$user_info['is_guest'])
        {
                $wgUser = User::newFromName( $user_info['username'] );
                if ( 0 != $wgUser->idForName() ) 
                {
			//echo '<br/>'.$user_info['email'];
                        // user exists in wiki  
                        // if you leave the cookies blank then the wiki user will always log out when SMF does
                        $wgUser->setCookies();
 
                }
                else
                {
                        // create new wiki user
                        // set properties
                        $wgUser->mEmail       = $user_info['email']; // Set Email Address.
                        $wgUser->mRealName    = $user_info['name'];  // Set Real Name.
                        $wgUser->addToDatabase();
                        $wgUser->setToken();
                        // if you leave the cookies blank then the wiki user will always log out when SMF does
                        //$wgUser->setCookies();
                }
        }

	return true;
}
/*function LogoutSMF($wgUser)
{
}
*/
$wgHooks['UserLoadFromSession'][] = 'AutoAuthenticateSMF';
//$wgHooks['UserLogout'][] = 'LogoutSMF';
 
 
/**
 * @package MediaWiki
 */
# Copyright (C) 2004 Brion Vibber <brion@pobox.com>
# http://www.mediawiki.org/
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html

/**
 * Authentication plugin interface. Instantiate a subclass of AuthPlugin
 * and set $wgAuth to it to authenticate against some external tool.
 *
 * The default behavior is not to do anything, and use the local user
 * database for all authentication. A subclass can require that all
 * accounts authenticate externally, or use it only as a fallback; also
 * you can transparently create internal wiki accounts the first time
 * someone logs in who can be authenticated externally.
 *
 * This interface is new, and might change a bit before 1.4.0 final is
 * done...
 *
 * @package MediaWiki
 */
    // First check if class has already been defined.
    if (!class_exists('AuthPlugin')) {
 
        /**
         * Auth Plugin
         *
         */
        require_once './includes/AuthPlugin.php';
 
    } // End: if (!class_exists('AuthPlugin')) {
 
class Auth_SMF extends AuthPlugin {

	function __construct() {
		//parent::__construct();
	}

        /**
         * Check whether there exists a user account with the given name.
         * The name will be normalized to MediaWiki's requirements, so
         * you might need to munge it (for instance, for lowercase initial
         * letters).
         *
         * @param string $username
         * @return bool
         * @access public
         */
        function userExists( $username ) {
                return true;
        }
 
        /**
         * Check if a username+password pair is a valid login.
         * The name will be normalized to MediaWiki's requirements, so
         * you might need to munge it (for instance, for lowercase initial
         * letters).
         *
         * @param string $username
         * @param string $password
         * @return bool
         * @access public
         */
        function authenticate( $username, $password ) {
                        return FALSE;
        }
 
        /**
         * Modify options in the login template.
         *
         * @param UserLoginTemplate $template
         * @access public
         */
        function modifyUITemplate( &$template ) {
                # Override this!
                $template->set('usedomain', false );
      $template->set('useemail', false); // Disable the mail new password box.
                $template->set('create', false); // Remove option to create new accounts from the wiki.
        }
 
        /**
         * Set the domain this plugin is supposed to use when authenticating.
         *
         * @param string $domain
         * @access public
         */
        function setDomain( $domain ) {
                $this->domain = $domain;
        }
 
        /**
         * Check to see if the specific domain is a valid domain.
         *
         * @param string $domain
         * @return bool
         * @access public
         */
        function validDomain( $domain ) {
                # Override this!
                return true;
        }
 
        /**
         * When a user logs in, optionally fill in preferences and such.
         * For instance, you might pull the email address or real name from the
         * external user database.
         *
         * The User object is passed by reference so it can be modified; don't
         * forget the & on your function declaration.
         *
         * @param User $user
         * @access public
         */
        function updateUser( &$user ) {
 
                return true;
        }
 
 
        /**
         * Return true if the wiki should create a new local account automatically
         * when asked to login a user who doesn't exist locally but does in the
         * external auth database.
         *
         * If you don't automatically create accounts, you must still create
         * accounts in some way. It's not possible to authenticate without
         * a local account.
         *
         * This is just a question, and shouldn't perform any actions.
         *
         * @return bool
         * @access public
         */
        function autoCreate() {
                return true;
        }
 
        /**
         * Set the given password in the authentication database.
         * Return true if successful.
         *
         * @param string $password
         * @return bool
         * @access public
         */
        function setPassword( $password ) {
                return false;
        }
 
        /**
         * Update user information in the external authentication database.
         * Return true if successful.
         *
         * @param User $user
         * @return bool
         * @access public
         */
        function updateExternalDB( $user ) {
                return true;
        }
 
        /**
         * Check to see if external accounts can be created.
         * Return true if external accounts can be created.
         * @return bool
         * @access public
         */
        function canCreateAccounts() {
                return false;
        }
 
        /**
         * Add a user to the external authentication database.
         * Return true if successful.
         *
         * @param User $user
         * @param string $password
         * @return bool
         * @access public
         */
        function addUser( $user, $password ) {
                return false;
        }
 
 
        /**
         * Return true to prevent logins that don't authenticate here from being
         * checked against the local database's password fields.
         *
         * This is just a question, and shouldn't perform any actions.
         *
         * @return bool
         * @access public
         */
        function strict() {
                return true;
        }
 
        /**
         * When creating a user account, optionally fill in preferences and such.
         * For instance, you might pull the email address or real name from the
         * external user database.
         *
         * The User object is passed by reference so it can be modified; don't
         * forget the & on your function declaration.
         *
         * @param User $user
         * @access public
         */
        function initUser(&$user) {
      return true;
        }
 
        /**
         * If you want to munge the case of an account name before the final
         * check, now is your chance.
         */
        function getCanonicalName( $username ) {
                global $user_info, $wdSMF_AdminNameCovert;
                if(empty($user_info['name']))
                        return $username;
 
                //Some Person need to Redirected to other usernames? (Example, for SysOps Redirect)!
                if($user_info['is_admin'] && isset($wdSMF_AdminNameCovert) && is_array($wdSMF_AdminNameCovert)) { //Only Admins :P
                        foreach($wdSMF_AdminNameCovert as $name => $toname)
                                if(strtolower($user_info['name']) == strtolower($name))
                                        return $toname;
                }
        return ucfirst(strtolower($user_info['name']));
        }
}
 
 
?>
