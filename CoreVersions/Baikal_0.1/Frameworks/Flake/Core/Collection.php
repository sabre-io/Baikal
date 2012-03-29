<?php

namespace Flake\Core;

class Collection extends \Flake\Core\FLObject implements \Iterator {
	protected $aCollection = array();
	protected $aMeta = array();
	
	public function current() {
		return current($this->aCollection);
	}
	
	public function key() {
		return key($this->aCollection);
	}
	
	public function next() {
		return next($this->aCollection);
	}
	
	public function rewind() {
		$this->reset();
	}
	
	public function valid() {
		$key = key($this->aCollection);
		return ($key !== NULL && $key !== FALSE);
	}
	
	public function getForKey($sKey) {
		$aKeys = $this->keys();
		if(!in_array($sKey, $aKeys)) {
			throw new \Exception("\Flake\Core\Collection->getForKey(): key '" . $sKey . "' not found in Collection");
		}
		
		return $this->aCollection[$sKey];
	}

	public function &each() {
		list($key, $val) = each($this->aCollection);
		return $val;
	}

	public function reset() {
		reset($this->aCollection);
	}

	public function prev() {
		return prev($this->aCollection);
	}

	/*
	 * Retourne le nombre d'élémént
	 *
	 * @return	int	nombre l'éléments
	 */
	public function count() {
		return count($this->aCollection);
	}

	/*
	 * Retourne le tableau des clés
	 *
	 * @return	array	tableau des clés
	 */
	public function keys() {
		return array_keys($this->aCollection);
	}

	/*
	 * Vérifie si la collection est vide
	 *
	 * @return	bool	TRUE si la collection est vide
	 */
	public function isEmpty() {
		return $this->count() === 0;
	}
	
	public function isAtFirst() {
		return $this->key() === array_shift($this->keys());
	}
	
	public function isAtLast() {
		return $this->key() === array_pop($this->keys());
	}

	/*
	 * Insère un élément dans la collection
	 *
	 * @param	$mMixed	valeur à insérer dans la collection
	 * @return	void
	 */
	public function push(&$mMixed) {
		array_push($this->aCollection, $mMixed);
	}

	/*
	 * Vide la collection
	 *
	 * @return	void
	 */
	public function flush() {
		unset($this->aCollection);
		$this->aCollection = array();
	}

	/*
	 * Retourne le premier élément de la collection
	 *
	 * @return	mixed	premier élément de la collection
	 */
	public function &first() {
		if(!$this->isEmpty()) {
			$aKeys = $this->keys();
			return $this->aCollection[array_shift($aKeys)];
		}

		return null;
	}
	
	public function &last() {
		if(!$this->isEmpty()) {
			$aKeys = $this->keys();
			return $this->aCollection[array_pop($aKeys)];
		}

		return null;
	}

	/*
	 * Converti la collection en tableau
	 *
	 * @return	array	collection sous forme de tableau
	 */
	public function toArray() {
		return $this->aCollection;
	}
	
	# Create a new collection like this one
	# This abstraction is useful because of CollectionTyped
	
	protected function newCollectionLikeThisOne() {
		$oCollection = \Flake\Core\Collection();
		return $oCollection;
	}

	/*
	 * Méthode magique __call
	 *
	 * @param	$sName	string	nom de la méthode
	 * @param	$aArguments	array		argument passé initialement
	 * @return 	mixed	valeur de la collection correspond
	 */
	public function &__call($sName, $aArguments) {
		if(
			strlen($sName) > 7 &&
			$sName{0} === "s" &&
			$sName{1} === "e" &&
			$sName{2} === "t" &&
			$sName{3} === "M" &&
			$sName{4} === "e" &&
			$sName{5} === "t" &&
			$sName{6} === "a"
		) {
			$sKey = strtolower(substr($sName, 7, 1)) . substr($sName, 8);
			$mValue =& $aArguments[0];

			if(is_null($mValue)) {
				if(array_key_exists($sKey, $this->aMeta)) {
					unset($this->aMeta[$sKey]);
				}
			} else {
				$this->aMeta[$sKey] =& $mValue;
			}

		} elseif(
			strlen($sName) > 7 &&
			$sName{0} === "g" &&
			$sName{1} === "e" &&
			$sName{2} === "t" &&
			$sName{3} === "M" &&
			$sName{4} === "e" &&
			$sName{5} === "t" &&
			$sName{6} === "a"
		) {
			$sKey = strtolower(substr($sName, 7, 1)) . substr($sName, 8);
			if(array_key_exists($sKey, $this->aMeta)) {
				return $this->aMeta[$sKey];
			} else {
				return null;
			}
		} else {
			throw new \Exception("Method " . $sName . "() not found on " . self::getClass());
		}
	}
}