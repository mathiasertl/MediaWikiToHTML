<?php

class SpecialToHTML extends SpecialPage {
    function __construct() {
        parent::__construct('ToHTML');
    }

	/**
	 * main worker-function
	 */
	function execute($par) {
		global $wgOut, $wgParser, $wgRequest;
		$this->setHeaders();

		// create title object from par, throw error if no par
		if ($par) {
			$title = Title::newFromtext($par);
		} else {
			$wgOut->addWikiText(wfMsg('noParamGiven'));
			return true;
		}

		// check if article exists
		if (! $title->exists()) {
			$wgOut->addWikiText(wfMsg('parNotExists', $par));
			return true;
		}

		$wgOut->addWikiText(wfMsg('pageHeader', $title->getPrefixedText()));

		$article = new Article($title);
		$article->loadContent();
		$content = "__NOEDITSECTION__\n" . $article->getContent();

		// delete banner on top:
		$content = preg_replace('/{{FAQinfo}}/', '', $content);
		//copy forward/backward banner to bottom:
		$navBlock = array();
		preg_match('/{{FAQ.*?}}/', $content, $navBlock);
#		$content .= "\n" . $navBlock[0];

		$parserOptions = new ParserOptions();
		$content = $wgParser->preprocess($content, $title, $parserOptions);
		$parserOutput = $wgParser->parse($content, $title, $parserOptions);

		$footerText = "\n<p>Diese Seite wurde auf <a href=\"http://vowi.fsinf.at/wiki/" . $title->getPrefixedText() . '">vowi.fsinf.at</a> geschrieben und steht unter der <a href="http://www.gnu.org/copyleft/fdl.html">GNU Free Documentation Licence 1.3</a>. Der Text entspricht der <a href="http://vowi.fsinf.at/wiki?title=' . $title->getPrefixedText() . '&oldid=' . $title->getLatestRevID() . '">Revision ' . $title->getLatestRevID() . "</a>.</p>\n";
		$footerPreProcess = $wgParser->preprocess($navBlock[0], $title, $parserOptions);
		$footerParserOutput = $wgParser->parse($footerPreProcess, $title, $parserOptions);
		$footerCode = $footerText . $footerParserOutput->getText();

		$wgOut->addParserOutputNoText($parserOutput);
		$htmlCode = $parserOutput->getText() . $footerCode;

		// insert <!--break--> tag, or drupal will break in the middle of TOC!
		$htmlCode = preg_replace('/(<table id="toc".*?)<\/table>/s', '$1</table><!--break-->', $htmlCode);

		// turn <span>s with an ID to "<t:span>"s, removing other attributes
		$htmlCode = preg_replace('/<span [^>]*id="(.*?)"[^>]*>(.*?)<\/span>/', '<t:span id="$1">$2</t:span>', $htmlCode);

		// eliminate <span> tags
		$htmlCode = preg_replace('/<span[^>]*>/', '', $htmlCode);
		$htmlCode = preg_replace('/<\/span>/', '', $htmlCode);

		// turn <t:span>s back into <span>s
		$htmlCode = preg_replace('/<t:span/', '<span', $htmlCode);
		$htmlCode = preg_replace('/<\/t:span>/', '</span>', $htmlCode);

		// create a drupal-TOC:
		$htmlCode = preg_replace('/<div id="toctitle"><h2>(.*?)<\/h2><\/div>/', '<b>$1</b>', $htmlCode);
		$htmlCode = preg_replace('/<table id="toc" class="toc"[^>]*?><tr><td>(.*?)<\/td><\/tr><\/table>/s', '<div class="node sticky">$1</div>', $htmlCode);
		$htmlCode = preg_replace('/ class="toclevel-."/', '', $htmlCode);

		// get rid of class=new in <a href
		$htmlCode = preg_replace('/ class="(new|external text)"/', '', $htmlCode);

		// fix links:
		// FAQ links should link to fsinf.at...
		$htmlCode = preg_replace('/href="\/wiki\/FAQ/', 'href="/infos/FAQ', $htmlCode);
		// other internal mediawiki-links have to include the URL of the wiki
		$htmlCode = preg_replace('/href="\/wiki/', 'href="http://vowi.fsinf.at/wiki', $htmlCode);
		// direct file links got to vowi
		$htmlCode = preg_replace('/href="\/images/', 'href="http://vowi.fsinf.at/images', $htmlCode);
		// links to fsinf.at can now be relative:
		$htmlCode = preg_replace('/href="http:\/\/(www.)?fsinf.at([^"])/', 'href="$2', $htmlCode);
		// get rid of nofollow:
		$htmlCode = preg_replace('/ rel="nofollow"/', '', $htmlCode);

		// fix spaces in links - only for fsinf.at links!:
		$matches = NULL;
		preg_match_all('/"\/.*?"/s', $htmlCode, $matches);
		foreach ($matches[0] as $match) {
			$match = preg_replace('/#.*/', '', $match);
			$replacement = preg_replace('/_/', '+', $match);
			$match = preg_quote($match, '/');
			$htmlCode = preg_replace('/' . $match . '/', $replacement, $htmlCode);
		}

		// get rid of some spaces
		$htmlCode = preg_replace('/(<h[1-5]>) *(.*?) *?(<\/h[1-5]>)/', '$1$2$3', $htmlCode);

		if ($wgRequest->getText('action') == "raw") {
			print(htmlspecialchars($htmlCode));
			die();
		} else {
			$wgOut->addWikiText('<pre>' . $htmlCode . '</pre>');
		}
#		$wgOut->addHTML ($htmlCode);


		return true;
	}
}
