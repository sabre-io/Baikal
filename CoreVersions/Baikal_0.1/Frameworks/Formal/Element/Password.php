<?php

namespace Formal\Element;

class Password extends \Formal\Element {
	
	public function render() {
		$disabled = "";
		$inputclass = "";
		$groupclass = "";
		$placeholder = "";
		
		$value = htmlspecialchars($this->value());
		$label = $this->option("label");
		$prop = $this->option("prop");
		$placeholder = $this->option("placeholder");
		
		if($this->option("readonly") === TRUE) {
			$inputclass .= " disabled";
			$disabled = " disabled";
		}
		
		if($this->option("error") === TRUE) {
			$groupclass .= " error";
		}
		
		if(($sPlaceHolder = trim($this->option("placeholder"))) !== "") {
			$placeholder = " placeholder=\"" . htmlspecialchars($sPlaceHolder) . "\" ";
		}
		
		$sHtml =<<<HTML
<div class="control-group{$groupclass}">
	<label class="control-label" for="displayname">{$label}</label>
	<div class="controls">
		<input type="password" class="input-xlarge{$inputclass}" id="{$prop}" name="{$prop}" value="{$value}"{$disabled}{$placeholder}/>
	</div>
</div>
HTML;
		return $sHtml;
	}
}