<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://formal.codr.fr
#
#  This script is part of the Formal project. The Formal
#  project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Formal\Element;

class Listbox extends \Formal\Element {
    function render() {
        $disabled = "";
        $inputclass = "";
        $groupclass = "";
        $placeholder = "";

        $value = $this->value();
        $label = $this->option("label");
        $prop = $this->option("prop");
        $helpblock = "";
        $popover = "";

        if ($this->option("readonly") === true) {
            $inputclass .= " disabled";
            $disabled = " disabled";
        }

        if ($this->option("error") === true) {
            $groupclass .= " error";
        }

        $aOptions = $this->option("options");
        if (!is_array($aOptions)) {
            throw new \Exception("\Formal\Element\Listbox->render(): 'options' has to be an array.");
        }

        if (($sHelp = trim($this->option("help"))) !== "") {
            $helpblock = "<p class=\"help-block\">" . $sHelp . "</p>";
        }

        if (($aPopover = $this->option("popover")) !== "") {
            $inputclass .= " popover-focus ";
            $popover = " title=\"" . htmlspecialchars($aPopover["title"]) . "\" ";
            $popover .= " data-content=\"" . htmlspecialchars($aPopover["content"]) . "\" ";
        }

        $clientvalue = htmlspecialchars($value);

        $aRenderedOptions = [];

        if (\Flake\Util\Tools::arrayIsSeq($aOptions)) {
            # Array is sequential
            reset($aOptions);
            foreach ($aOptions as $sOptionValue) {
                $selected = ($sOptionValue === $value) ? " selected=\"selected\"" : "";
                $aRenderedOptions[] = "<option" . $selected . ">" . htmlspecialchars($sOptionValue) . "</option>";
            }
        } else {
            # Array is associative
            reset($aOptions);
            foreach ($aOptions as $sOptionValue => $sOptionCaption) {
                $selected = ($sOptionValue === $value) ? " selected=\"selected\"" : "";
                $aRenderedOptions[] = "<option value=\"" . htmlspecialchars($sOptionValue) . "\"" . $selected . ">" . htmlspecialchars($sOptionCaption) . "</option>";
            }
        }

        reset($aRenderedOptions);
        $sRenderedOptions = implode("\n", $aRenderedOptions);
        unset($aRenderedOptions);

        $sHtml = <<<HTML
	<div class="control-group{$groupclass}">
		<label class="control-label" for="{$prop}">{$label}</label>
		<div class="controls">
			<select class="{$inputclass}" id="{$prop}" name="data[{$prop}]"{$disabled}{$popover}>
				{$sRenderedOptions}
			</select>
			{$helpblock}
		</div>
	</div>
HTML;

        return $sHtml . $this->renderWitness();
    }
}
