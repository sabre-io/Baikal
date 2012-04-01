<?php

namespace Formal\Element;

class Text extends \Formal\Element {
	
	public function render() {
		$value = htmlspecialchars($this->value());
		$label = $this->option("label");
		$prop = $this->option("prop");
		$disabled = "";
		$inputclass = "";
		$groupclass = "";
		
		if($this->option("readonly") === TRUE) {
			$inputclass .= " disabled";
			$disabled = " disabled";
		}
		
		if($this->option("error") === TRUE) {
			$groupclass .= " error";
		}
		
		$sHtml =<<<HTML
<div class="control-group{$groupclass}">
	<label class="control-label" for="displayname">{$label}</label>
	<div class="controls">
		<input type="text" class="input-xlarge{$inputclass}" id="{$prop}" name="{$prop}" value="{$value}"{$disabled} />
	</div>
</div>
HTML;
		return $sHtml;
	}
}