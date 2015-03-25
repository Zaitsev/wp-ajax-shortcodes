<?
//define('UNIT_TESTING',true);
require_once './tests/emsgd.php';
require_once '../vendor/autoload.php';
use tad\FunctionMocker\FunctionMocker;
FunctionMocker::setUp();


require_once './tests/functions.php';


FunctionMocker::replace( 'add_action' );
FunctionMocker::replace( 'add_shortcode' );
define('ABSPATH',true);

require_once '../ajax-shortcode/AjaxShortcode.php';