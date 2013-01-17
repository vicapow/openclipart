<?php
/**
 *  This file is part of Open Clipart Library <http://openclipart.org>
 *
 *  Open Clipart Library is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  Open Clipart Library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Open Clipart Library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  author: Jakub Jankiewicz <http://jcubic.pl>
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 'On');

define('DEBUG', true);

/** we do this to prevent MAMP's include_path settings from interfering. 
	* specificially: MAMP/bin/php/php5.4.4/lib/php/System.php
	*/
set_include_path('.:');

require_once ('vendor/autoload.php');
require_once ('libs/utils.php');
require_once ('libs/Template.php');
require_once ('libs/System.php');
require_once ('libs/Clipart.php');
require_once ('libs/OCAL.php');

// config twig
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);



/* TODO: logs (using slim) - same as apacha with gzip and numbering
 *                           cache all exceptions and log them
 *       cache in Template::render
 *          {{%cache_time:week}}  mustache pragma
 *
 *
 */


$app = new OCAL(array(
    'db_prefix' => 'openclipart',
    'tag_limit' => 30,
    'top_artist_last_month_limit' => 10,
    'home_page_thumbs_limit' => 9,
    'home_page_collections_limit' => 5,
    'home_page_news_limit' => 3,
    'token_expiration' => 1, // number of hours for token expiration (token send via email)
    'bitmap_resolution_limit' => 3840, // number from old javascript
    'google_analytics' => false,
    // permission to functions in
    'permissions' => array(
        // JSON-RPC permissions
        'rpc' => array(
            'Admin' => array('admin')
        ),
        'access' => array(
            'disguise' => array('admin', 'developer'),
            'add_to_group' => array('admin'),
        ),
        // disguise fun is silent by default - executed in System constructor
        'silent' => array(),
        'disabled' => array()
    ),
    'show_facebook' => false,
    'debug' => true,
    // user     disguise as this user
    // track    initialy to disable download count in edit button
    //          can be use in different places
    // size     thumbail_size
    // token    you can browse site without cookies and php sessions
    //          using token in url, token will be send for users that forget
    //          passwords
    //          if token_expiration in database is null the time is infinite
    // sort     download, favorites, date
    // desc     for sort true or false
    // lang     for translation system
    'forward_query_list' => array(
      'nsfw'    => '/^(true|false)$/i',
      'track'   => '/^(true|false)$/i',
      'user'    => '/^[0-9]+$/',
      'size'    => '/^[0-9]+(px|%)?$/i',
      'token'   => '/^[0-9a-f]{40}$/i',
      'sort'    => '/^(name|date|download|favorites)$/i',
      'desc'    => '/^(true|false)$/i',
      'lang'    => '/^(pl|es|js|de|zh)$/i'
    ),
    'nsfw_image' => array(
        'user' => 'h0us3s'
        , 'filename' => 'h0us3s_Signs_Hazard_Warning_1'
    ),
    'pd_issue_image' => array(
        'user' => 'h0us3s'
        , 'filename' => 'h0us3s_Signs_Hazard_Warning_1'
    ),
    'missing_avatar_image' => array(
        'user' => 'pianoBrad'
        , 'filename' => 'openclipart-logo-grey'
    ),
));

require_once('routes/errors.php');
require_once('routes/index.php');
require_once('routes/login.php');
require_once('routes/forgot-password.php');
require_once('routes/profile.php');
require_once('routes/logout.php');
require_once('routes/register.php');
require_once('routes/chat.php');
require_once('routes/clipart.php');
require_once('routes/download.php');
require_once('routes/image.php');
require_once('routes/search.php');

$app->get("/about", function() use($twig) {
    return $twig->render('about.template');
});

$app->get("/participate", function() use($twig){
    return $twig->render('participate.template');
});

$app->get("/why-the-ads", function() use($twig){
    return $twig->render('why-the-ads.template');
});

$app->get('/test', function() use($twig){
    return $twig->render('test.template');
});

$app->notFound(function () use ($twig) {
    return $twig->render('errors/404.template');
});

$app->get("/profile-test", function() use($twig) { //added just to be able to work on css. can be removed once profiles are available
    return $twig->render('profile-test.template');
});

$app->run();

?>

