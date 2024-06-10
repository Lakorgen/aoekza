<?
/**
 * MV - content management framework for developing internet sites and applications.
 * Released under the terms of BSD License.
 * http://mv-framework.ru
 * http://mv-framework.com
 */

//Main config file with all settings and autoloads
require_once "config/autoload.php";

//Set 1 to see the work time and sql queries
$debug = new Debug(0); 

//Main object of site also contains all modules objects
$mv = new Builder();

include_once $mv -> views_path."main-index-include.php";

//Router refers to include needed view to display the page
include_once $mv -> router -> defineRoute();

//If 1 was passed above, displays the data
$debug -> displayInfo($mv -> router);
?>