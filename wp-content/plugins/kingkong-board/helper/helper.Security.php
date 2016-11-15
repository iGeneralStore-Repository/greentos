<?php
/**
 * 킹콩보드 워드프레스 게시판 보안 함수
 * @link www.superrocket.io
 * @copyright Copyright 2015 SuperRocket. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl.html
 */
	// HTMLPurifier Include
	if(!class_exists('HTMLPurifier')){
		require_once(KINGKONGBOARD_ABSPATH.'htmlpurifier/library/HTMLPurifier.safe-includes.php');
	}

/**
 * Load HTMLPurifier with HTML5, TinyMCE, YouTube, Video support.
 *
 * Copyright 2014 Alex Kennberg (https://github.com/kennberg/php-htmlpurifier-html5)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function load_htmlpurifier($board_id, $type = null, $allowed, $allowed_iframe ) {

	if(get_option('charset')){
		$charset = get_option('charset');
	} else {
		$charset = 'UTF-8';
	} 
  $config = HTMLPurifier_Config::createDefault();
  $config->set( 'HTML.Doctype', 'HTML 4.01 Transitional' );
  $config->set( 'Core.Encoding', $charset );
  $config->set('CSS.AllowTricky', true);

  if($type != null){
    if($type == 'post' && get_post_meta($board_id, 'kkb_auto_link', true) == 'T'){
      $config->set('AutoFormat.Linkify', true);
      $config->set('HTML.TargetBlank', true);
    }
    if($type == 'comment' && get_post_meta($board_id, 'kkb_comment_auto_link', true) == 'T'){
      $config->set('AutoFormat.Linkify', true);
      $config->set('HTML.TargetBlank', true);
    }
  }


  $config->set( 'Cache.SerializerPath', WP_CONTENT_DIR.'/uploads' );
  
  $config->set('HTML.SafeIframe', true);
  // Allow iframes from:
	$config->set('URI.SafeIframeRegexp', '#^(http:|https:)?//(?:'.implode('|', $allowed_iframe ).')#');
  $config->set('HTML.Allowed', implode(',', $allowed));

  // Set some HTML5 properties
  $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
  $config->set('HTML.DefinitionRev', 1);
  if ($def = $config->maybeGetRawHTMLDefinition()) {
    // http://developers.whatwg.org/sections.html
    $def->addElement('section', 'Block', 'Flow', 'Common');
    $def->addElement('nav',     'Block', 'Flow', 'Common');
    $def->addElement('article', 'Block', 'Flow', 'Common');
    $def->addElement('aside',   'Block', 'Flow', 'Common');
    $def->addElement('header',  'Block', 'Flow', 'Common');
    $def->addElement('footer',  'Block', 'Flow', 'Common');
    // Content model actually excludes several tags, not modelled here
    $def->addElement('address', 'Block', 'Flow', 'Common');
    $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
    // http://developers.whatwg.org/grouping-content.html
    $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
    $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
    // http://developers.whatwg.org/the-video-element.html#the-video-element
    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
      'src' => 'URI',
      'type' => 'Text',
      'width' => 'Length',
      'height' => 'Length',
      'poster' => 'URI',
      'preload' => 'Enum#auto,metadata,none',
      'controls' => 'Bool',
    ));
    $def->addElement('source', 'Block', 'Flow', 'Common', array(
      'src' => 'URI',
      'type' => 'Text',
    ));
    // http://developers.whatwg.org/text-level-semantics.html
    $def->addElement('s',    'Inline', 'Inline', 'Common');
    $def->addElement('var',  'Inline', 'Inline', 'Common');
    $def->addElement('sub',  'Inline', 'Inline', 'Common');
    $def->addElement('sup',  'Inline', 'Inline', 'Common');
    $def->addElement('mark', 'Inline', 'Inline', 'Common');
    $def->addElement('wbr',  'Inline', 'Empty', 'Core');
    // http://developers.whatwg.org/edits.html
    $def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
    $def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
    // TinyMCE
    $def->addAttribute('img', 'data-mce-src', 'Text');
    $def->addAttribute('img', 'data-mce-json', 'Text');
    // Others
    $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
    $def->addAttribute('table', 'height', 'Text');
    $def->addAttribute('td', 'border', 'Text');
    $def->addAttribute('th', 'border', 'Text');
    $def->addAttribute('tr', 'width', 'Text');
    $def->addAttribute('tr', 'height', 'Text');
    $def->addAttribute('tr', 'border', 'Text');
  }
  return new HTMLPurifier($config);
}

function kingkongboard_xssfilter($board_id, $type = null, $data){

	$allowed_list = array('p[align|style],strong,b,em,table[class|width|cellpadding],td,tr,h3,h4,h5,hr,br,u,ul,ol,li,img[src|width|height|alt|class],iframe[src|width|height|alt|class|frameborder|allowfullscreen],span[class],strike,sup,sub,video[src|type|width|height|poster|preload|controls],a[href]');
	$allowed  			= apply_filters('kkb_xssfilter_allowed', $allowed_list, $board_id);
	$allowed_iframe = kingkongboard_allowed_iframe($board_id);
	$Purifier 			= load_htmlpurifier($board_id, $type, $allowed, $allowed_iframe );
	$data 					= @$Purifier->purify( stripslashes( $data ) );
	return $data;
}

function kingkongboard_allowed_iframe($board_id){
	$allowed_iframes = array(
    'www\\.youtube(?:-nocookie)?\\.com/',
    'maps\\.google\\.com/',
    'player\\.vimeo\\.com/video/',
    'www\\.microsoft\\.com/showcase/video\\.aspx',
    '(?:serviceapi\\.nmv|player\\.music)\\.naver\\.com/',
    '(?:api\\.v|flvs|tvpot|videofarm)\\.daum\\.net/',
    'v\\.nate\\.com/',
    'play\\.mgoon\\.com/',
    'channel\\.pandora\\.tv/',
    'www\\.tagstory\\.com/',
    'play\\.pullbbang\\.com/',
    'tv\\.seoul\\.go\\.kr/',
    'ucc\\.tlatlago\\.com/',
    'vodmall\\.imbc\\.com/',
    'www\\.musicshake\\.com/',
    'www\\.afreeca\\.com/player/Player\\.swf',
    'static\\.plaync\\.co\\.kr/',
    'video\\.interest\\.me/',
    'player\\.mnet\\.com/',
    'sbsplayer\\.sbs\\.co\\.kr/',
    'img\\.lifestyler\\.co\\.kr/',
    'c\\.brightcove\\.com/',
    'www\\.slideshare\\.net/',
	);

	return apply_filters('kkb_xssfilter_allowed_iframe', $allowed_iframes, $board_id);
}

/**
 * 모든 html 제거
 * @param object $data
 */
function kingkongboard_htmlclear($data){
	return htmlspecialchars(strip_tags($data));
}
?>