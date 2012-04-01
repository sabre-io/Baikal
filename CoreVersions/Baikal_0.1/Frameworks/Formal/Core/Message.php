<?php

namespace Formal\Core;

class Message {
	private function __construct() {
	}
	
	public static function error($sMessage) {
		$sHtml =<<<HTML
<div id="confirm" class="alert alert-block alert-error">
	<h3 class="alert-heading">Validation error</h3>
	{$sMessage}
</div>
HTML;
		return $sHtml;
	}
	
	public static function notice($sMessage) {
		$sHtml =<<<HTML
<div id="info" class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	{$sMessage}
</div>
HTML;
		return $sHtml;
	}
	
	public static function warningConfirmMessage($sHeader, $sDescription, $sActionUrl, $sActionLabel, $sCancelUrl, $sCancelLabel="Cancel") {
		$sHtml =<<<HTML
<div id="confirm" class="alert alert-block alert-error">
	<!--a class="close" data-dismiss="alert" href="#">&times;</a-->
	<h3 class="alert-heading">{$sHeader}</h3>
	{$sDescription}
	<p>
		<a class="btn btn-danger" href="{$sActionUrl}">{$sActionLabel}</a> <a class="btn" href="{$sCancelUrl}">Cancel</a>
	</p>
</div>
HTML;
		return $sHtml;
	}
}