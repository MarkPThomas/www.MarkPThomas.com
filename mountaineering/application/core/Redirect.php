<?php

namespace markpthomas\mountaineering;

/**
 * Class Redirect
 *
 * Simple abstraction for redirecting the user to a certain page.
 */
class Redirect
{

    /**
     * To the last visited page before user logged in (useful when people are on a certain page inside your application
     * and then want to log in (to edit or comment something for example) and don't to be redirected to the main page).
     *
     * This is just a bulletproof version of Redirect::to(), redirecting to an ABSOLUTE URL path like
     * "http://www.mydomain.com/user/profile", useful as people had problems with the RELATIVE URL path generated
     * by Redirect::to() when using HUGE inside sub-folders.
     *
     * @param string $path
     */
    public static function toPreviousViewedPageAfterLogin($path)
    {
        header('location: http://' . $_SERVER['HTTP_HOST'] . '/' . $path);
    }

    /**
     * To the homepage.
     */
    public static function home()
    {
        header("location: " . Config::get('URL'));
    }

    /**
     * To the defined page, uses a relative path (like "user/profile").
     *
     * Redirects to a RELATIVE path, like "user/profile" (which works very fine unless you are using HUGE inside tricky
     * sub-folder structures).
     *
     * @see https://github.com/panique/huge/issues/770
     * @see https://github.com/panique/huge/issues/754
     *
     * @param string $path Relative path to redirect to.
     */
    public static function to($path)
    {
        header("location: " . Config::get('URL') . $path);

        // Note that the page might not refresh (such as when loading the same page.
        // @see https://stackoverflow.com/questions/4221116/php-refresh-current-page/4221146
        // Below should fix this.
//        echo '<script type="text/javascript">location.reload(true);</script>';
//        die;
        // Still is not working even with this :-/
    }

    /**
     * To the defined page, uses a relative path (like "user/profile").
     * Uses JavaScript such that this still works after headers have been sent. <br />
     *
     * Redirects to a RELATIVE path, like "user/profile" (which works very fine unless you are using HUGE inside tricky
     * sub-folder structures).
     *
     * @param string $path Relative path to redirect to. If blank, redirects to home.
     *
     * @see https://stackoverflow.com/questions/7066527/redirect-a-user-after-the-headers-have-been-sent
     */
    public static function toWithJS($path = '')
    {
        $string = '<script type="text/javascript">';
        $string .= 'window.location = "' . Config::get('URL') . $path . '"';
        $string .= '</script>';

        echo $string;
    }

}
