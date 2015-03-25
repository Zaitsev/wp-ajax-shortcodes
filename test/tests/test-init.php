<?
use tad\FunctionMocker\FunctionMocker;

class AjaxShortcodeClass extends PHPUnit_Framework_TestCase {
	use Codeception\Specify;
	protected $wp_send_json;
	protected $add_action;
	protected $add_shortcode;
	protected $func;
	public function setUp() {
		FunctionMocker::setUp();
		FunctionMocker::replace('AjaxShortcode::safe_die', 'Lorem ipsum');

//		WP_Mock::setUp();
		global $shortcode_tags;
		$shortcode_tags=array();
		unset($_GET);
	}

	public function tearDown() {
	}
	public function testShortcodeWrap(){
		$GLOBALS['shortcode_tags'] = array('test'=>array('tag'=>'new'));
		$this->func    = FunctionMocker::replace( 'render_shortcode');
		do_shortcode('[test id=1]');
		$args = [['id'=>'1'],null,'test'];
		$this->func->wasCalledWithOnce($args);
	}
	public function testShortcodeParams(){
		$this->wp_send_json = FunctionMocker::replace( 'wp_send_json');
		$this->func    = FunctionMocker::replace( 'render_shortcode',function($atts,$con,$tag){emsgd(func_get_args());return 'test';});
		$GLOBALS['shortcode_tags'] = array('ajax_good_attr'=>'a','ajax_good_no_called'=>'a','wrong'=>'b');
		$_GET['shortcode']='ajax_good_attr';
		$_GET['dataset']['id']=1;
		$_GET['dataset']['attr']=2;
		$args = [['id'=>"1",'attr'=>"2"],null,'ajax_good_attr'];
		AjaxShortcode::ajax_shortcode_callback();
		$this->func->wasCalledOnce();
		$this->func->wasCalledWithOnce($args);
	}
	public function testInit(){
		$this->beforeSpecify( function () {
			$this->add_action    = FunctionMocker::replace( 'add_action' );
			$this->add_shortcode = FunctionMocker::replace( 'add_shortcode' );
			new AjaxShortcode();
			unset($_GET);
		} );
		$this->specify('this regiser no-priv ajax call',function(){
			$args = array( 'wp_ajax_nopriv_ajax_shortcode', array( 'AjaxShortcode', 'ajax_shortcode_callback' ) );
			$this->add_action->wasCalledWithOnce( $args );
		});
		$this->specify('this regiser priv ajax call',function(){
			$args = array( 'wp_ajax_ajax_shortcode', array( 'AjaxShortcode', 'ajax_shortcode_callback' ) );
			$this->add_action->wasCalledWithOnce( $args );
		});
		$this->specify('this regiser wp_enqueue_scripts',function(){
			$args = array( 'wp_enqueue_scripts', array( 'AjaxShortcode', 'ajax_shortcode_JS_CSS' ) );
			$this->add_action->wasCalledWithOnce( $args );
		});
	}
	public function testAjaxCallBasic(){
		$this->beforeSpecify( function () {
			$this->wp_send_json = FunctionMocker::replace( 'wp_send_json');
		} );
//
//      ---------------- wp_send_json died auto
//      $this->specify('It must die after send',function(){
//			$die = FunctionMocker::replace( 'AjaxShortcode::safe_die' );
//			AjaxShortcode::ajax_shortcode_callback();
//			$die->wasCalledOnce( );
//		});
		$this->specify('It must use wp_send_json',function(){
			AjaxShortcode::ajax_shortcode_callback();
			$this->wp_send_json->wasCalledOnce( );
		});
	}

	public function testAjax(){
		$this->beforeSpecify( function () {
			$this->wp_send_json = FunctionMocker::replace( 'wp_send_json',  function($arg){
				return json_encode( $arg );
			} );
//			$this->wp_send_json = FunctionMocker::replace('wp_die');
			$this->do_shortcode = FunctionMocker::replace('do_shortcode');
			unset($_GET);

		} );
		$this->specify('It must do only ajax_ prefixed',function(){
//			global $shortcode_tags ;
			$GLOBALS['shortcode_tags'] = array('ajax_good'=>'a','ajax_good_no_called'=>'a','wrong'=>'b');
			$_GET['shortcode']='ajax_good';
			AjaxShortcode::ajax_shortcode_callback();
			$this->do_shortcode->wasCalledWithOnce(array('[ajax_good]'));
		});
		$this->specify('It must return valid Object',function(){
//			global $shortcode_tags ;
			$this->assertInstanceOf('stdClass',AjaxShortcode::callback(),'Must be stdClass');
		});
		$this->specify('It must return valid JSON',function(){
//			global $shortcode_tags ;
			$this->assertJson(AjaxShortcode::ajax_shortcode_callback(),'Must be JSON');
		});
	}

}
