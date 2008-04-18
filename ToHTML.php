<?php
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/MyExtension/MyExtension.php" );
EOT;
	exit( 1 );
}

$dir = dirname(__FILE__);

$wgAutoloadClasses['ToHTML'] = $dir . '/SpecialToHTML.php';
$wgExtensionMessagesFiles['ToHTML'] = $dir . '/ToHTML.i18n.php';
$wgSpecialPages[ 'ToHTML'] = 'ToHTML';
$wgHooks['LanguageGetSpecialPageAliases'][] = 'efToHTMLLocalizedPageName';

$wgExtensionCredits['specialpage'][] = array (
	'name' => 'ToHTML',
	'description' => 'Converts a given page to its representation in HTML, with a few tweaks for a drupal-system. Note that this is highly customized for our usage.',
	'version' => '1.0.2-1.12.0',
	'author' => 'Mathias Ertl',
	'url' => 'http://pluto.htu.tuwien.ac.at/devel_wiki/ToHTML',
);

function efToHTMLLocalizedPageName( &$specialPageArray, $code) {
	wfLoadExtensionMessages('ToHTML');
	$textMain = wfMsgForContent('tohtml');
        $textUser = wfMsg('tohtml');

        # Convert from title in text form to DBKey and put it into the alias array:
        $titleMain = Title::newFromText( $textMain );
        $titleUser = Title::newFromText( $textUser );
        $specialPageArray['ToHTML'][] = $titleMain->getDBKey();
        $specialPageArray['ToHTML'][] = $titleUser->getDBKey();
	
	return true;
}
