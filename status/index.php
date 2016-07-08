<?php
/**
 * start page for webaccess
 * redirect the user to the supported page type by the users webbrowser (js available or not)
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: index.php 687 2012-09-06 20:54:49Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
/**
 * define the application root path on the webserver
 * @var string
 */
        include "../includes/authenticate.inc.php";
        include "../includes/config.inc.php";
        $access_level = access_level($username);
        $management_of = "ip";

define('APP_ROOT', dirname(__FILE__));

/**
 * internal xml or external
 * external is needed when running in static mode
 *
 * @var boolean
 */
define('PSI_INTERNAL_XML', false);

if (version_compare("5.1.3", PHP_VERSION, ">")) {
    die("PHP 5.1.3 or greater is required!!!");
}
if (!extension_loaded("pcre")) {
    die("phpSysInfo requires the pcre extension to php in order to work properly.");
}

require_once APP_ROOT.'/includes/autoloader.inc.php';

// Load configuration
require_once APP_ROOT.'/read_config.php';

if (!defined('PSI_CONFIG_FILE') || !defined('PSI_DEBUG')) {
    $tpl = new Template("/templates/html/error_config.html");
    echo $tpl->fetch();
    die();
}

// redirect to page with and without javascript
$display = strtolower(isset($_GET['disp']) ? $_GET['disp'] : PSI_DEFAULT_DISPLAY_MODE);
switch ($display) {
case "static":
    $webpage = new WebpageXSLT();
    $webpage->run();
    break;
case "dynamic":
    $webpage = new Webpage();
    $webpage->run();
    break;
case "xml":
    $webpage = new WebpageXML(true, null);
    $webpage->run();
    break;
case "bootstrap":
/*
    $tpl = new Template("/templates/html/index_bootstrap.html");
    echo $tpl->fetch();
*/
    $webpage = new Webpage("bootstrap");
    $webpage->run();
    break;
case "auto":
    $tpl = new Template("/templates/html/index_all.html");
    echo $tpl->fetch();
    break;
default:
    $defaultdisplay = strtolower(PSI_DEFAULT_DISPLAY_MODE);
    switch ($defaultdisplay) {
    case "static":
        $webpage = new WebpageXSLT();
        $webpage->run();
        break;
    case "dynamic":
        $webpage = new Webpage();
        $webpage->run();
        break;
    case "bootstrap":
        $webpage = new Webpage("bootstrap");
        $webpage->run();
        break;
    default:
        $tpl = new Template("/templates/html/index_all.html");
        echo $tpl->fetch();
        break;
    }
    break;
}
