<?php

namespace Formal\Element;

class Text extends \Formal\Element {
	
	public function render() {
		$value = htmlspecialchars($this->value());
		$label = $this->option("label");
		$prop = $this->option("prop");
		$disabled = "";
		$class = "";
		
		if($this->option("readonly") === TRUE) {
			$class = " disabled";
			$disabled = " disabled";
		}
		
		$sHtml =<<<HTML
<div class="control-group">
	<label class="control-label" for="displayname">{$label}</label>
	<div class="controls">
		<input type="text" class="input-xlarge{$sClass}" id="{$prop}" name="{$prop}" value="{$value}"{$disabled} />
	</div>
</div>
HTML;
		return $sHtml;
	}
}