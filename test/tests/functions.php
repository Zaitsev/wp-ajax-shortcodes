<?

function render_shortcode($atts,$cont,$tag){
	emsgd($tag);
	return $tag;
}

/**
 *
 *  WP original code for testings
 *
 *
 *
 */
function do_shortcode($content) {
global $shortcode_tags;

if (empty($shortcode_tags) || !is_array($shortcode_tags))
return $content;

$pattern = get_shortcode_regex();
return preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $content );
}

/**
* Retrieve the shortcode regular expression for searching.
*
* The regular expression combines the shortcode tags in the regular expression
* in a regex class.
*
* The regular expression contains 6 different sub matches to help with parsing.
*
* 1 - An extra [ to allow for escaping shortcodes with double [[]]
* 2 - The shortcode name
* 3 - The shortcode argument list
* 4 - The self closing /
* 5 - The content of a shortcode when it wraps some content.
* 6 - An extra ] to allow for escaping shortcodes with double [[]]
*
* @since 2.5
* @uses $shortcode_tags
*
* @return string The shortcode search regular expression
*/
function get_shortcode_regex() {
global $shortcode_tags;
$tagnames = array_keys($shortcode_tags);
$tagregexp = join( '|', array_map('preg_quote', $tagnames) );

// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
// Also, see shortcode_unautop() and shortcode.js.
return
'\\['                              // Opening bracket
. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
. "($tagregexp)"                     // 2: Shortcode name
. '(?![\\w-])'                       // Not followed by word character or hyphen
. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
.     '(?:'
.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
.     ')*?'
. ')'
. '(?:'
.     '(\\/)'                        // 4: Self closing tag ...
.     '\\]'                          // ... and closing bracket
. '|'
.     '\\]'                          // Closing bracket
.     '(?:'
.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
.             '[^\\[]*+'             // Not an opening bracket
.             '(?:'
.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
.                 '[^\\[]*+'         // Not an opening bracket
.             ')*+'
.         ')'
.         '\\[\\/\\2\\]'             // Closing shortcode tag
.     ')?'
. ')'
. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
}

function shortcode_parse_atts($text) {
	$atts = array();
	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) and strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
	} else {
		$atts = ltrim($text);
	}
	return $atts;
}

function do_shortcode_tag( $m ) {
	global $shortcode_tags;

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag = $m[2];
	$attr = shortcode_parse_atts( $m[3] );

//	emsgd( $m[3]);
//	emsgd($attr);
	return render_shortcode($attr,null,$tag);
//	if ( isset( $m[5] ) ) {
//		// enclosing tag - extra parameter
//		return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
//	} else {
//		// self-closing tag
		//return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, null,  $tag ) . $m[6];
//	}

}

