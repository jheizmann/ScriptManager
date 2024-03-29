<?php
/*
 * Copyright (C) ontoprise GmbH
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program.If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Script extension which manages the inclution of common JS script libraries.
 *
 * @author Kai K�hn
 *
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file is part of the Script Manager extension. It is not a valid entry point.\n" );
}

define('SCM_VERSION', '{{$VERSION}} [B{{$BUILDNUMBER}}]');

// buildnumber index for MW to define a script's version.
$scmgStyleVersion = preg_replace('/[^\d]/', '', '{{$BUILDNUMBER}}' );
if (strlen($scmgStyleVersion) > 0) {
    $scmgStyleVersion= '?'.$scmgStyleVersion;
}

global $wgExtensionFunctions, $wgScriptPath;
$smgSMIP = $IP . '/extensions/ScriptManager';
$smgSMPath = $wgScriptPath . '/extensions/ScriptManager';

$wgExtensionFunctions[] = 'smgSetupExtension';

function smgSetupExtension() {
	global $wgHooks, $wgExtensionCredits;
	$wgHooks['BeforePageDisplay'][]='smfAddHTMLHeader';
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'smfMergeHead';
	
	// Register Credits
    $wgExtensionCredits['parserhook'][]= array('name'=>'ScriptManager&nbsp;Extension', 'version'=>SCM_VERSION,
            'author'=>"Maintained by [http://smwplus.com ontoprise GmbH].", 
            'url'=>'http://smwforum.ontoprise.com/smwforum/index.php/Help:Script_Manager_Extension',
            'description' => 'Organizes javascript libraries.');
    
    global $wgResourceModules, $smgSMIP, $smgSMPath;
    
	$moduleTemplate = array(
		'localBasePath' => $smgSMIP,
		'remoteBasePath' => $smgSMPath,
		'group' => 'ext.ScriptManager'
	);
    
    $wgResourceModules['ext.ScriptManager.prototype'] = $moduleTemplate + array(
		'scripts' => array(
    			'scripts/prototype.js',
    			'scripts/prototype.binding.js'
				),
		'styles' => array(
				)
	);
	
    $wgResourceModules['ext.jquery.qtip'] = $moduleTemplate + array(
		'scripts' => array(
    			'scripts/qTip/qtip2/jquery.qtip.js'
				),
		'styles' => array(
        'scripts/qTip/qtip2/jquery.qtip.css'
				)
	);
	
    $wgResourceModules['ext.jquery.fancybox'] = $moduleTemplate + array(
		'scripts' => array(
    			'scripts/fancybox/jquery.fancybox-1.3.4.js'
				),
		'styles' => array(
				'/scripts/fancybox/jquery.fancybox-1.3.4.css'
				)
	);

    $wgResourceModules['ext.jquery.query'] = $moduleTemplate + array(
		'scripts' => array(
    			'scripts/query/jquery.query-2.1.7.js'
				)
	);
    
    $wgResourceModules['ext.smwhalo.json2'] = $moduleTemplate + array(
		'scripts' => array(
    			'scripts/json2.js'
				)
	);

     $wgResourceModules['ext.jquery.tree'] = $moduleTemplate + array(
        'scripts' => array(
            'scripts/jstree/jquery.jstree.js',
            'scripts/jstree/_lib/jquery.hotkeys.js'
        ),
        'dependencies' => 'jquery.cookie'
	);

     $wgResourceModules['ext.jquery.jec'] = $moduleTemplate + array(
        'scripts' => array(
            'scripts/jec/jquery.jec-1.3.3.js'
        )
	);

     $wgResourceModules['jquery.ui.combobox'] = $moduleTemplate + array(
        'scripts' => array(
            'scripts/jquery.ui.combobox/jquery.ui.combobox.js'
        ),
         'styles' => array(
          'scripts/jquery.ui.combobox/jquery.ui.combobox.css'
				),
         'dependencies' => array(
             'jquery.ui.autocomplete',
             'jquery.ui.button'
        )
	);



    
}

function smfAddHTMLHeader(& $out) {
	global $smgJSLibs, $smgSMPath, $smwgDeployVersion, $scmgStyleVersion;
    static $outputSend;
    if (isset($outputSend) || !is_array($smgJSLibs)) return true;
	$smgJSLibs = array_unique($smgJSLibs);
	$smgJSLibs = smfSortScripts($smgJSLibs);
	foreach($smgJSLibs as $lib_id) {

		switch($lib_id) {
			case 'prototype':
				$out->addModules('ext.ScriptManager.prototype');
				break;
			case 'jquery':
//				if ( method_exists( 'OutputPage', 'includeJQuery' ) ) {
//					$out->includeJQuery();
//					//make it not conflicting with other libraries like prototype
//					$out->addScript("<script type=\"text/javascript\">var \$jq = jQuery.noConflict();</script>");
//				} else {
//					$out->addScript("<script type=\"text/javascript\" src=\"". "$smgSMPath/scripts/jquery.js$scmgStyleVersion\"></script>");
//					global $smwgJQueryIncluded;
//					$smwgJQueryIncluded = true;
//				}
				break;
			case 'qtip':
				$out->addModules('ext.jquery.qtip');
				break;
			case 'json':
        $out->addModules('ext.smwhalo.json2');
//                if (isset($smwgDeployVersion) && $smwgDeployVersion !== false)
//					$out->addScript("<script type=\"text/javascript\" src=\"". "$smgSMPath/scripts/json2.min.js$scmgStyleVersion\"></script>");
//                else
//					$out->addScript("<script type=\"text/javascript\" src=\"". "$smgSMPath/scripts/json2.js$scmgStyleVersion\"></script>");
				break;
			case 'fancybox':
				$out->addModules('ext.jquery.fancybox');
				break;
			case 'ext':
				$out->addLink($smgSMPath.'/scripts/extjs/resources/css/ext-all.css'.$scmgStyleVersion, 'screen, projection');
				$out->addScript('<script type="text/javascript" src="' . $smgSMPath . '/scripts/extjs/adapter/ext/ext-base.js'.$scmgStyleVersion.'"></script>');
				$out->addScript('<script type="text/javascript" src="' . $smgSMPath . '/scripts/extjs/ext-all.js'.$scmgStyleVersion.'"></script>');
				break;
						
		}
	}
    $outputSend = true;
	return true;
}

function smfSortScripts($smgJSLibs) {
	$newList = array();
	if (in_array('jquery', $smgJSLibs)) {
		$newList[] = 'jquery';
	}
	foreach($smgJSLibs as $lib) {
		if ($lib != 'jquery') {
			$newList[] = $lib;
		}
	}
	return $newList;
}

/**
 * MW enables multiple extensions. Different extensions may use same (css in) js frameworks.
 * This function is for the hook 'SkinTemplateOutputPageBeforeExec'
 * and it calls 'smfMergeHeadLinks' and 'smwfMergeHeadScript' to merge
 * known js/css links for multiple included frameworks
 * 
 * @param type $skin
 * @param type $skinTemplate
 * @return boolean true
 */
function smfMergeHead( $skin, $skinTemplate ) {
	// FIXME: For vector skin all scripts and (css) links are contained in
	// $skinTemplate->data['headelements']
	if ( $skinTemplate && $skinTemplate->data ) {
		if ( array_key_exists( 'headscripts', $skinTemplate->data ) ) {
			// actual head scripts of SkinTemplate
			$headScripts = $skinTemplate->data['headscripts'];
			// merged head scripts of SkinTemplate
			$mergedHeadScripts = smfMergeHeadScripts( $headScripts );
			$skinTemplate->set( 'headscripts', $mergedHeadScripts );
		}
		if ( array_key_exists( 'headlinks', $skinTemplate->data ) ) {
			//actual head links of SkinTemplate
			//TODO: these aren't all. Where are all the links?
			$headLinks = $skinTemplate->data['headlinks'];
			// merged head links of SkinTemplate
			$mergedHeadLinks = smfMergeHeadLinks( $headLinks );
			$skinTemplate->set( 'headlinks', $mergedHeadLinks );
		}
		if ( array_key_exists( 'csslinks', $skinTemplate->data ) ) {
			$cssLinks = $skinTemplate->data['csslinks'];
			$mergedCssLinks = smfMergeHeadLinks( $cssLinks );
			$skinTemplate->set( 'csslinks', $mergedCssLinks );
		}
		if ( array_key_exists( 'headelement', $skinTemplate->data ) ) {
			// vector based skin use the headelement which also contains the doctype, meta tags etc.
			$headElement = $skinTemplate->data['headelement'];
			// find first occurences of link and script tags
			// everything before the first link
			$headIntro = substr( $headElement,
				0,
				strpos( $headElement, '<link', 1 ) -1
			);
			$cssLinks = substr( $headElement,
				strpos( $headElement, '<link', 1 ),
				strpos( $headElement, '<script', 1 ) - strpos( $headElement, '<link', 1 ) - 1 
			);
			$headScripts =  substr( $headElement,
				strpos( $headElement, '<script', 1 )
			);
			// merge and set them again
			$mergedCssLinks = smfMergeHeadLinks( $cssLinks );
			$mergedHeadScripts = smfMergeHeadScripts( $headScripts );
			$skinTemplate->set( 'headelement', 
				$headIntro . $mergedCssLinks . $mergedHeadScripts
			);
		}
	}
	return true;
}

/**
 * MW enables multiple extensions. Different extensions may use same css in js frameworks.
 * In order to avoid css conflict, this function will merge same css links (based on filename pattern)
 *
 * Non-framework css files in different extensions may have the same filename,
 * this may cause HTML rendering bugs
 *
 * This function will be called from smfMergeHead
 *
 * @param string $headLinks
 * @return headlinks merged
 */
function smfMergeHeadLinks( $headLinks ) {
	// apply common link pattern, <link ... href="LINK_FILE" ... />
	preg_match_all( '/\<\s*link\b[^\>]+\brel\s+\bhref\s*=\s*[\'"]([^\'"]*)[\'"][^\>]*\/\>/i', $headLinks, $links, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	$newlink = ''; // new head link string
	$offset = 0; // offset in $headLinks
	$ls = array( ); // a keyword list to store css file strings
	foreach ( $links as $l ) {
		// append head string outside link patterns
		$newlink .= substr( $headLinks, $offset, $l[0][1] - $offset );
		// calculate new offset
		$offset = $l[0][1] + strlen( $l[0][0] );
		// get file keyword (file name only), e.g. 'extensions/EA/css/jquery.css', the keyword is 'jquery.css'
		$start = strrpos( $l[1][0], '/' );
		$key = substr( $l[1][0], ($start === false ? -1 : $start) + 1 );
		// if the css keyword is first used, append to head link, otherwise, omit it
		if ( !isset( $ls[$key] ) ) {
			$newlink .= $l[0][0];
			$ls[$key] = true;
		}
	}
	// append the rest head string
	$newlink .= substr( $headLinks, $offset );

	return $newlink;
}

/**
 * MW enables multiple extensions. Different extensions may use same js frameworks.
 * In order to avoid js conflict, this function will merge same js srces (based on filename pattern)
 * Also, it renders multiple js frameworks in proper sequence.
 *
 * Non-framework js files in different extensions may have the same filename,
 * this may cause HTML js bugs
 *
 * This function will be called from smfMergeHead
 *
 * @param string $scripts
 * @return scripts merged
 */
function smfMergeHeadScripts( $scripts ) {
	// split head scripts with pattern '</script>', which will always be a script end mark
	$sc = preg_split( '/\<\s*\/script\s*\>/i', $scripts );
	$newscript = ''; // new head script string
	$ls = array( ); // a keyword list to store js file strings
	// registered js frameworks, jquery, jqueryui, prototype, extjs, yui, ...
	$js_frameworks = array(
		'jquery' => false,
		'jqueryui' => false,
		'jqueryfancybox' => false,
		'prototype' => false,
		'yui' => false,
		'extjs' => false
	);
	
	global $scmgStyleVersion;
	$scmgStyleVersionQuoted = preg_quote($scmgStyleVersion);
	foreach ( $sc as $s ) {
		// test if current script piece is in common script src pattern, <script ... src="JS_FILE" ... >
		if ( preg_match( '/\<\s*script\b[^\>]+\bsrc\s*=\s*[\'"]([^\'"]*)[\'"][^\>]*\>/i', $s, $script, PREG_OFFSET_CAPTURE ) )
		{
			// append head string outside script patterns
			$newscript .= substr( $s, 0, $script[0][1] );
			// get file keyword (file name only)
			// e.g. 'extensions/EA/scripts/jquery.js', the keyword is 'jquery.js'
			$start = strrpos( $script[1][0], '/' );
			$key = substr( $script[1][0], ($start === false ? -1 : $start) + 1 );
			// judge common js frameworks with filename patterns
			if ( preg_match( '/^jquery(-[\d]+(\.[\d]+)*)?(\.min)?\.js'.$scmgStyleVersionQuoted.'\b/i', $key ) ) {
				// jquery, jquery.js / jquery-1.3.2.js / jquery-1.3.2.min.js / jquery.min.js
				$js_frameworks['jquery'] = true;
			} else if ( preg_match( '/\bjquery-ui(-[\d]+(\.[\d]+)*)?(\.min)?\.js'.$scmgStyleVersionQuoted.'\b/i', $key ) ) {
				// jquery-ui.js / jquery-ui-1.7.2.js / jquery-ui-1.7.2.min.js
				$js_frameworks['jqueryui'] = true;
			} else if ( preg_match( '/\bjquery.fancybox(-[\d]+(\.[\d]+)*)?(\.min)?\.js'.$scmgStyleVersionQuoted.'\b/i', $key ) ) {
				// jquery's fancybox plugin
				if( $js_frameworks['jquery'] ) {
					// jquery has to be included before
					$js_frameworks['jqueryfancybox'] = true;
				} else {
					// otherwise, just append js piece
					$newscript .= $s . '</script>';
				}
			} else if ( preg_match( '/\bprototype(-[\d]+(\.[\d]+)*)?(\.min)?\.js'.$scmgStyleVersionQuoted.'\b/i', $key ) ) {
				// prototype, prototype.js / prototype-1.6.0.js / prototype-1.6.0.min.js
				$js_frameworks['prototype'] = true;
			} else if ( preg_match( '/\bext-[^\.]+\.js'.$scmgStyleVersionQuoted.'\b/i', $key ) ) {
				// extjs, ext-all.js / ext-base.js / ext-jquery-adapter.js / ...
				$js_frameworks['extjs'] = true;
			} else {
				// if the js keyword is first used, append to head script, otherwise, omit it
				if ( !isset( $ls[$key] ) ) {
					$newscript .= substr( $s, $script[0][1] ) . '</script>';
					$ls[$key] = true;
				}
			}
		} else {
			// just append js piece
			$newscript .= $s . '</script>';
		}
	}
	$newscript = substr( $newscript, 0, strlen( $newscript ) - strlen( '</script>' ) );

	// generate framework scripts
	global $wgJsMimeType, $smgSMPath;
	$frameworks = '';
	if ( $js_frameworks['jquery'] ) {
		// jquery with noConflict flag
		$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/jquery-1.3.2.min.js$scmgStyleVersion\"></script>\n";
		$frameworks .= "<script type=\"{$wgJsMimeType}\">jQuery.noConflict();jQuery.noConflict=function( deep ) {return jQuery;};</script>\n";
	}
	if ( $js_frameworks['jqueryui'] ) {
		// jquery ui
		$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/jquery-ui-1.7.2.custom.min.js$scmgStyleVersion\"></script>\n";
	}
	if ( $js_frameworks['jqueryfancybox'] ) {
		// jQuery's fancybox plugin
		$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/fancybox/jquery.fancybox-1.3.4.js$scmgStyleVersion\"></script>\n";
	}
	if ( $js_frameworks['prototype'] ) {
		// prototype
		$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/prototype.js$scmgStyleVersion\"></script>\n";
	}
	if ( $js_frameworks['extjs'] ) {
		// extjs with multiple adapter
		if ( $js_frameworks['prototype'] ) {
			$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/extjs/adapter/prototype/ext-prototype-adapter.js$scmgStyleVersion\"></script>\n";
		} else if ( $js_frameworks['yui'] ) {
			$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/extjs/adapter/yui/ext-yui-adapter.js$scmgStyleVersion\"></script>\n";
		} else if ( $js_frameworks['jquery'] ) {
			$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/extjs/adapter/jquery/ext-jquery-adapter.js$scmgStyleVersion\"></script>\n";
		} else {
			$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/extjs/adapter/ext/ext-base.js$scmgStyleVersion\"></script>\n";
		}
		$frameworks .= "<script type=\"{$wgJsMimeType}\" src=\"{$smgSMPath}/scripts/extjs/ext-all.js$scmgStyleVersion\"></script>\n";
	}
	// add js framework to top
	return $frameworks . $newscript;
}
