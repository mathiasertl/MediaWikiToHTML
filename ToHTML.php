<?php
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/MyExtension/MyExtension.php" );
EOT;
	exit( 1 );
}

$wgAutoloadClasses['ToHTML'] = dirname(__FILE__) . '/SpecialToHTML.php';
$wgSpecialPages[ 'ToHTML'] = 'ToHTML';
$wgHooks['LoadAllMessages'][] = 'ToHTML::loadMessages';
$wgHooks['LangugeGetSpecialPageAliases'][] = 'ToHTML_LocalizedPageName';

function ToHTML_LocalizedPageName( &$specialPageArray, $code) {
	ToHTML::loadMessages();
	$text = wfMsg('tohtml');

	# Convert from title in text form to DBKey and put it into the alias array:
	$title = Title::newFromText( $text );
	$specialPageArray['ToHTML'][] = $title->getDBKey();

	return true;
}
