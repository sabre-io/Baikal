<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://bootstrap.codr.fr
#
#  This script is part of the CodrBootstrap project. The CodrBootstrap project
#  is free software; you can redistribute it
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

namespace Flake\Core\DOM;

class HTMLElement extends \DOMElement {
    function getInnerText() {
        return $this->nodeValue;
    }

    function getOuterHTML() {
        return $this->ownerDocument->saveHTML($this);
    }

    function getNormalizedInnerText() {
        return $this->normalizeWhiteSpace($this->getInnerText());
    }

    function getNormalizedOuterHTML() {
        return $this->normalizeWhitespace($this->getOuterHTML());
    }

    protected function normalizeWhitespace($sText) {
        $sText = str_replace(["\t", "\r\n", "\n"], ' ', $sText);

        # using multiple str_replace has proven to be twice as fast that regexp on big strings
        $iCount = 0;
        do {
            $sText = str_replace('  ', ' ', $sText, $iCount);
        } while ($iCount > 0);

        return trim($sText);
    }

    function setInnerHTML($sHtml) {
        // first, empty the element
        for ($x = $this->childNodes->length - 1; $x >= 0; --$x) {
            $this->removeChild($this->childNodes->item($x));
        }
        // $value holds our new inner HTML
        if ($sHtml != '') {
            $f = $this->ownerDocument->createDocumentFragment();
            // appendXML() expects well-formed markup (XHTML)
            $result = @$f->appendXML($sHtml); // @ to suppress PHP warnings
            if ($result) {
                if ($f->hasChildNodes()) {
                    $this->appendChild($f);
                }
            } else {
                // $value is probably ill-formed
                $f = new \DOMDocument();
                $sHtml = mb_convert_encoding($sHtml, 'HTML-ENTITIES', 'UTF-8');
                // Using <htmlfragment> will generate a warning, but so will bad HTML
                // (and by this point, bad HTML is what we've got).
                // We use it (and suppress the warning) because an HTML fragment will
                // be wrapped around <html><body> tags which we don't really want to keep.
                // Note: despite the warning, if loadHTML succeeds it will return true.
                $result = @$f->loadHTML('<htmlfragment>' . $sHtml . '</htmlfragment>');
                if ($result) {
                    $import = $f->getElementsByTagName('htmlfragment')->item(0);
                    foreach ($import->childNodes as $child) {
                        $importedNode = $this->ownerDocument->importNode($child, true);
                        $this->appendChild($importedNode);
                    }
                } else {
                    // oh well, we tried, we really did. :(
                    // this element is now empty
                }
            }
        }
    }

    function getInnerHTML() {
        $sHtml = '';
        $iNodes = $this->childNodes->length;
        for ($i = 0; $i < $iNodes; ++$i) {
            $oItem = $this->childNodes->item($i);
            $sHtml .= $oItem->ownerDocument->saveHTML($oItem);
        }

        return $sHtml;
    }

    function isDOMText() {
        return $this->nodeType === XML_TEXT_NODE;
    }

    function getSiblingPosition() {
        $iPos = 0;
        $oNode = $this;

        while (!is_null($oNode->previousSibling)) {
            $oNode = $oNode->previousSibling;
            ++$iPos;
        }

        return $iPos;
    }

    function getTreePosition() {
        # Tree position is number 100^level + sibling offset
        $iLevel = substr_count($this->getNodePath(), "/") - 2;    # -1 to align on 0, and -1 to compensate for /document
        if ($iLevel === 0) {
            return $this->getSiblingPosition();
        } else {
            return pow(10, $iLevel) + $this->getSiblingPosition();
        }
    }
}
