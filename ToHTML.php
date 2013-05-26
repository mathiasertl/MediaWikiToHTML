<?php
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/MyExtension/MyExtension.php" );
EOT;
	exit(1);
}

$wgAutoloadClasses['SpecialToHTML'] = __DIR__ . '/SpecialToHTML.php';
$wgExtensionMessagesFiles['ToHTML'] = __DIR__ . '/ToHTML.i18n.php';
$wgExtensionMessagesFiles['ToHTMLAlias'] = __DIR__ . '/ToHTML.alias.php';
$wgSpecialPages['ToHTML'] = 'SpecialToHTML';

$wgExtensionCredits['specialpage'][] = array (
    'path' => __file__,
	'name' => 'ToHTML',
	'description' => 'Converts a given page to its representation in HTML, with a few tweaks for a drupal-system. Note that this is highly customized for our usage.',
	'version' => '1.0.5-1.21.0',
	'author' => 'Mathias Ertl',
	'url' => 'http://fs.fsinf.at/wiki/ToHTML',
);
