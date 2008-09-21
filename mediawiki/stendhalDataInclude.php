<?php
if (!defined('MEDIAWIKI')) {
	die("<b>Stendhal Data Include</b> is a MediaWiki extension not intended to be used on its own.");
}

require_once($IP.'/../scripts/xml.php');
require_once($IP.'/../scripts/items.php');

$wgExtensionFunctions[] = "stendhalDataIncludeSetup";
$wgExtensionCredits['other'][] = array(
	'version' => '0.1',
	'name' => 'StendhalDataInclude',
	'author' => 'Hendrik Brummermann',
	'url' => 'http://arianne.sf.net',
	'description' => 'Include Stendhal Data<br>'
		. '&lt;item&gt;rod of the gm&lt;item&gt;<br>'
		. 'info="default" <em>or</em> type="icon" <em>or</em> type="stats"<br>'
		. 'type="default" <em>or</em> type="mouseover" <em>or</em> type="include"<br>'
);



function getItemByName($name) {
	$items = getItems();

	foreach($items as $item) {
		if($item->name==$name) {
			return $item;
		}
	}
	return NULL;
}

/*
object(Item)#256 (6) {
  ["name"]=>
  string(13) "rod of the gm"
  ["description"]=>
  string(129) "You see a rod of the GM.  This rod is reserved for those with special powers, be careful not to let it fall into the wrong hands."
  ["class"]=>
  string(4) "club"
  ["gfx"]=>
  string(59) "itemimage.php?url=data/sprites/items/club/rod_of_the_gm.png"
  ["attributes"]=>
  array(3) {
    ["atk"]=>
    string(3) "100"
    ["def"]=>
    string(3) "100"
    ["rate"]=>
    string(1) "2"
  }
  ["equipableat"]=>
  NULL
}
*/


function stendhalDataIncludeItemIconOnly($item) {
	$res .= '<span class="stendhalItemIcon"><a href="/?id=content/scripts/item&name=' . urlencode($item->name) . '&exact">';
	$res .= '<img src="/' . htmlspecialchars($item->gfx) . '" />';
	$res .= '</a></span>';
	return $res;
}

function stendhalDataIncludeItemStats($item, $argv) {
	$res .= '<div class="stendhalItem"><span class="stendhalItemIconNameBanner">';

	if (!isset($argv['info'])) {
		$res .= stendhalDataIncludeItemIconOnly($item);
	}

	if (!isset($argv['info']) || ($argv['info'] == 'stats')) {
		$res .= '<a href="/?id=content/scripts/item&name=' . urlencode($item->name) . '&exact">';
		$res .= $item->name;
		$res .= '</a>';
	}
	$res .= '</span>';
		
	if (!isset($argv['info']) || ($argv['info'] == 'stats')) {
		$res .= '<br />';
		$res .= 'Class: ' . htmlspecialchars(ucfirst($item->class)) . '<br />';
		foreach($item->attributes as $label=>$data) {
			if ($label != "quantity") {
				$res .= htmlspecialchars(ucfirst($label)) . ': ' . htmlspecialchars($data) . '<br />';
			}
		}
	}
	if (!isset($argv['info'])) {
		$res .= '<br />' . $item->description . '<br />';
	}

	$res .= '</div>';
	return $res;
}

function stendhalDataIncludeItem($input, $argv, &$parser) {
	$res = '';
	$item = getItemByName($input);
	if ($item == NULL) {
		return '&lt;item "' . htmlspecialchars($input) . '" not found&gt;';
	}

	if (isset($argv['info']) && ($argv['info'] == 'icon')) {
		$res .= stendhalDataIncludeItemIconOnly($item);
		$block = false;
	} else {
		$res .= stendhalDataIncludeItemStats($item, $argv);
		$block = true;
	}

	if (!isset($argv['type']) || ($argv['type'] == 'mouseover')) {
		$popup = $res;
		$res = '';
		$res .= '<a href="/?id=content/scripts/item&name=' . urlencode($item->name) . '&exact"';
		$res .= ' onmouseover="return overlib(\''.rawurlencode($popup).'\', FGCOLOR, \'#000\', BGCOLOR, \'#FFF\',';
		$res .= 'DECODE, FULLHTML';
		$res .= ');" onmouseout="return nd();" class="stendhalItemLink">';
		$res .= htmlspecialchars($item->name);
		$res .= '</a>';
	}

	return $res;
}

function stendhalDataIncludeSetup() {
	global $wgParser, $wgScriptPath, $wgOut;
	$wgParser->setHook( 'item', 'stendhalDataIncludeItem' );

	$wgOut->addHTML('<script type="text/javascript" src="' . $wgScriptPath . '/extensions/overlibmws/overlibmws.js"></script>');
	$wgOut->addHTML('<script type="text/javascript" src="' . $wgScriptPath . '/extensions/overlibmws/overlibmws_filter.js" /></script>');
}

?>
