<?php

namespace markpthomas\mountaineering;

/**
 * Configuration for DEVELOPMENT environment
 * To create another configuration set just copy this file to config.production.php etc. You get the idea :)
 */

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard / no errors in production.
 * It's a little bit dirty to put this here, but who cares. For development purposes it's totally okay.
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Configuration for cookie security
 * Quote from PHP manual: Marks the cookie as accessible only through the HTTP protocol. This means that the cookie
 * won't be accessible by scripting languages, such as JavaScript. This setting can effectively help to reduce identity
 * theft through XSS attacks (although it is not supported by all browsers).
 *
 * @see php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
 */
ini_set('session.cookie_httponly', 1);

/**
 * Returns the full configuration.
 * This is used by the core/Config class.
 */
return array(
    // ===================== Paths ===================== 
    /**
     * Configuration for: Base URL
     * This detects your URL/IP incl. sub-folder automatically. You can also deactivate auto-detection and provide the
     * URL manually. This should then look like 'http://192.168.33.44/' ! Note the slash in the end.
     */
    'URL' => 'http://' . $_SERVER['HTTP_HOST'] . str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'])),

    /**
     * Configuration for: Folders
     * Usually there's no reason to change this.
     */
    'PATH_CONTROLLER' => realpath(dirname(__FILE__).'/../../') . '/application/controller/',
    'PATH_VIEW' => realpath(dirname(__FILE__).'/../../') . '/application/view/',
    
    /**
     * Configuration for: Avatar paths
     * Internal path to save avatars. Make sure this folder is writable. The slash at the end is VERY important!
     */
    'PATH_AVATARS' => realpath(dirname(__FILE__).'/../../') . '/public/avatars/',
    'PATH_AVATARS_PUBLIC' => 'avatars/',

    /**
     * Path to data stored by web crawlers.
     */
    'PATH_CRAWLER_DATA' => realpath(dirname(__FILE__).'/../../') . '/public/crawler/',

    /**
     * Configuration for: Default controller and action
     */
    'DEFAULT_CONTROLLER' => 'home',
    'DEFAULT_ACTION' => 'index',

    // ===================== Header Defaults =====================
    'HEADER_DEFAULT_TITLE' => 'Mountaineering',
    'HEADER_DEFAULT_SUBJECT' => 'Mountaineering',
    'HEADER_DEFAULT_KEYWORDS' => 'Mark Thomas, climbing, mountaineering, rock climbing, ice climbing, offwidth, artOfOffwidth, wide fetish',
    'HEADER_DEFAULT_DESCRIPTION' => 'A webpage for my mountaineering-related activities, such as hiking, rock climbing, ice climbing, skiing, and snowshoeing.',
    'HEADER_DEFAULT_IMAGE' => 'favicon-32x32.png',

    // ===================== Database ===================== 
    /**
     * Configuration for: Database (Default)
     */

    /**
     * The used database type. Note that other types than "mysql" might break the db construction currently.
     */
    'DB_TYPE' => 'mysql',

    /**
     * The mysql hostname, usually localhost or 127.0.0.1.
     */
    'DB_HOST' => 'localhost',

    /**
     * The database name.
     */
    'DB_NAME' => 'markptho_outdoors',

    /**
     * The username.
     */
    'DB_USER' => 'root',

    /**
     * The password.
     */
    'DB_PASS' => '',

    /**
     * The mysql port, 3306 by default (?), find out via phpinfo() and look for mysqli.default_port.
     */
    'DB_PORT' => '3306',

    /**
     * The charset, necessary for security reasons. Check Database.php class for more info.
     */
    'DB_CHARSET' => 'utf8',

    // ===================== Database - Photos (Piwigo) =====================
    /**
     * Configuration for: Database (Photos), using Piwigo service
     */
    'DB_PIWIGO_TYPE' => 'mysql',
    'DB_PIWIGO_HOST' => 'localhost',
    'DB_PIWIGO_NAME' => 'markptho_photos',
    'DB_PIWIGO_USER' => 'root',
    'DB_PIWIGO_PASS' => '',
    'DB_PIWIGO_PORT' => '3306',
    'DB_PIWIGO_CHARSET' => 'utf8',

    'DB_PIWIGO_ADMIN_USERNAME' => 'MarkPThomas',
    'DB_PIWIGO_ADMIN_PASSWORD' => 'ecb2rjtUC9955',

    // ===================== Database - CMS Demo =====================
    /**
     * Configuration for: Database for CMS demo from Udemy course
     */
    'DB_CMS_TYPE' => 'mysql',
    'DB_CMS_HOST' => 'localhost',
    'DB_CMS_NAME' => 'markptho_cms',
    'DB_CMS_USER' => 'root',
    'DB_CMS_PASS' => '',
    'DB_CMS_PORT' => '3306',
    'DB_CMS_CHARSET' => 'utf8',


    // ===================== Captcha ===================== 
    /**
     * Configuration for: Captcha size
     * The currently used Captcha generator (https://github.com/Gregwar/Captcha) also runs without giving a size,
     * so feel free to use ->build(); inside CaptchaModel.
     */
    'CAPTCHA_WIDTH' => 359,
    'CAPTCHA_HEIGHT' => 100,
    
    // ===================== Cookies ===================== 
     /**
    * How long should a cookie be valid by seconds, 1209600 seconds = 2 weeks
    */
    'COOKIE_RUNTIME' => 1209600,
    
    /**
    * The path the cookie is valid on, usually "/" to make it valid on the whole domain.
     * @see http://stackoverflow.com/q/9618217/1114320
     * @see php.net/manual/en/function.setcookie.php
    */
    'COOKIE_PATH' => '/',
    
    /**
    * The domain where the cookie is valid for. Usually this does not work with "localhost",
     * ".localhost", "127.0.0.1", or ".127.0.0.1". If so, leave it as empty string, false or null.
     * When using real domains make sure you have a dot (!) in front of the domain, like ".mydomain.com". This is
     * strange, but explained here:
     * @see http://stackoverflow.com/questions/2285010/php-setcookie-domain
     * @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     * @see http://php.net/manual/en/function.setcookie.php#73107
    */
    'COOKIE_DOMAIN' => "",
    
    /**
    * If the cookie will be transferred through secured connection(SSL). It's highly recommended to set it to true if you have secured connection.
    */
    'COOKIE_SECURE' => false,
    
    /**
    * If set to true, Cookies that can't be accessed by JS - Highly recommended!
    */
    'COOKIE_HTTP' => true,
    
    /**
    * How long should a session cookie be valid by seconds, 604800 = 1 week.
    */
    'SESSION_RUNTIME' => 604800,
    
    // ===================== Avatars ===================== 
    /**
     * Configuration for: Avatars/Gravatar support
     * Set to true if you want to use "Gravatar(s)", a service that automatically gets avatar pictures via using email
     * addresses of users by requesting images from the gravatar.com API. Set to false to use own locally saved avatars.
     */
    'USE_GRAVATAR' => false,
    'GRAVATAR_DEFAULT_IMAGESET' => 'mm',
    'GRAVATAR_RATING' => 'pg',
    
    /**
    * Set the pixel size of avatars/gravatars (will be 44x44 by default). Avatars are always squares.
    */
    'AVATAR_SIZE' => 44,
    
    'AVATAR_JPEG_QUALITY' => 85,
    
    /**
    * The default image in public/avatars/
    */
    'AVATAR_DEFAULT_IMAGE' => 'default.jpg',
    
    
    // ===================== Encryption ===================== 
    /**
     * Configuration for: Encryption Keys
     * ENCRYPTION_KEY, HMAC_SALT: Currently used to encrypt and decrypt publicly visible values, like the user id in
     * the cookie. Change these values for increased security, but don't touch if you have no idea what this means.
     */
    'ENCRYPTION_KEY' => '6#x0gÊìf^25cL1f$08&',
    'HMAC_SALT' => '8qk9c^4L6d#15tM8z7n0%',
    
    
    // ===================== Email =====================
    /**
     *  Email address to send contact page emails to.
     */
    'EMAIL_WEBMASTER' => 'markums@gmail.com',

    /**
     *  Domain Name to associate with the subject of emails sent from the contact page.
     */
    'DOMAIN_NAME' => 'MPT-Mountaineering',

    /**
     * Configuration for: Email server credentials
     *
     * Here you can define how you want to send emails.
     * If you have successfully set up a mail server on your linux server and you know
     * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
     * and fill in your SMTP provider account data.
     */
    
    /**
     * Check Mail class for alternatives. Currently has placeholders for 'swiftmailer' and 'native'.
     */
    'EMAIL_USED_MAILER' => 'phpmailer',

    /**
     * Use SMTP or not.
     * If not, PHP's mail() function will be used.
     */
    'EMAIL_USE_SMTP' => true,

    // Mail Services: SMTP (e.g. for announcements, password reset, etc.)
    // From: https://mailtrap.io/inboxes/320386/settings
    // Sign in w/ Google Account
    // For GMail, see: https://www.lifewire.com/what-are-the-gmail-smtp-settings-1170854

    'EMAIL_SMTP_HOST' => 'smtp.mailtrap.io',

    /**
     * Leave this true unless your SMTP service does not need authentication.
     */
    'EMAIL_SMTP_AUTH' => true,

    'EMAIL_SMTP_USERNAME' => '838cdb44f22511',
    'EMAIL_SMTP_PASSWORD' => '7d827d4c4f511c',
    'EMAIL_SMTP_PORT' => 2525,
    'EMAIL_SMTP_ENCRYPTION' => 'tls',       // 'tls', 'ssl' // Enable TLS encryption, `ssl` also accepted. See: https://luxsci.com/blog/ssl-versus-tls-whats-the-difference.html

    /**
     * Configuration for: Email content data
     */
    'EMAIL_PASSWORD_RESET_URL' => 'login/verifypasswordreset',
    'EMAIL_PASSWORD_RESET_FROM_EMAIL' => 'no-reply@MarkPThomasMountaineering.com',
    'EMAIL_PASSWORD_RESET_FROM_NAME' => 'Mark P Thomas - Mountaineering',
    'EMAIL_PASSWORD_RESET_SUBJECT' => 'Password reset for markpthomas.com/mountaineering',
    'EMAIL_PASSWORD_RESET_CONTENT' => 'Please visit this link to reset your password: ',

    'EMAIL_VERIFICATION_URL' => 'register/verify',
    'EMAIL_VERIFICATION_FROM_EMAIL' => 'no-reply@MarkPThomasMountaineering.com',
    'EMAIL_VERIFICATION_FROM_NAME' => 'Mark P Thomas - Mountaineering',
    'EMAIL_VERIFICATION_SUBJECT' => 'Account activation for markpthomas.com/mountaineering',
    'EMAIL_VERIFICATION_CONTENT' => 'Please visit this link to activate your account: ',

    // ===================== Push Notifications =====================
    'PUSHER_APP_ID' => "462413",
    'PUSHER_KEY' => "35f3b012142def86a574",
    'PUSHER_SECRET' => "4839fb8327e1e2dd4147",

    'PUSHER_CLUSTER' => "us2",
    'PUSHER_CHANNEL_NOTIFICATIONS' => 'notifications',
    'PUSHER_CHANNEL_EVENT' => 'new_user',

    // ===================== reCAPTCHA =====================
    'GOOGLE_RECAPTCHA' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
    'GOOGLE_RECAPTCHA_SECRET' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',

    // ===================== API YouTube =====================
    // Overall id: fluent-horizon-107804

    // Define API tokens for various YouTube APIs
    'BROWSER' => 'AIzaSyDDxdkwreCQ6OfeydUiU9HgZywjnObiG5I',
    'SERVER' => 'AIzaSyDrPyUs5EYmTrFrbXoyCrp8NmeIDRZp294',

    // ===================== API GoogleMaps =====================
    'Key' => 'AIzaSyBpdq2kXA7Hl7fQumZXRR6YrFC_CF0-LC4',
);
