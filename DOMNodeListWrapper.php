<?php

/**
* @class DOMNodeListWrapper
* @author Robert McLeod <hamstar@telescum.co.nz>
* @date 23/04/2010
* @version 0.1b
* @copyright 2009 Robert McLeod
*/
class DOMNodeListWrapper {

	public $length = 0;
	private $DOMNodeList = array();

	/**
	* This constructor can convert a DOMNodeList that it is
	* given into a DOMNodeListWrapper with DOMNodeWrapper objects
	* inside.  This gives expanded features to the list.
	*
	* @param object $DOMNodeList A DOMNodeList object to be converted
	*/
	function __construct( $DOMNodeList=null ) {
	
		if ( !is_null($DOMNodeList) ) {
			// Run through each node
			foreach ( $DOMNodeList as $n ) {
				if ( get_class($n) == 'DOMNodeWrapper' ) {
					$this->DOMNodeList[] = $n; // node already wrapped
				} else {
					$this->DOMNodeList[] = new DOMNodeWrapper($n); // wrap the node
				}
				
				$this->length++;
			}
		
		}
	
	}
	
	/**
	* This list can be put in a foreach loop!!
	*/
	function __invoke($i=null) {
		if ( !is_null($i) ) {
			return $this->DOMNodeList[$i];
		}
		
		return $this->DOMNodeList;
	}
	
	/**
	* For backwards compatibility - returns list index specified
	*
	* @param int $i List index to return
	*
	* @return object
	*/
	function item($i) {
		return isset($this->DOMNodeList[$i]) ? $this->DOMNodeList[$i]: false;
	}
	
	/**
	* Add a node to the list
	*
	* @param object $DOMNode The DOM Node to add
	*
	* @return object
	*/
	function addNode( $DOMNode ) {
		$this->DOMNodeList[] = new DOMNodeWrapper( $DOMNode );
		$this->length++;
		return $this;
	}
	
	/**
	* Remove a node from the list
	*
	* @param int $i The list index to remove
	*
	* @return object
	*/
	function removeNode( $i ) {
		unset( $this->DOMNodeList[$i] );
		$this->length--;
	}
	
	/**
	* Return the first item
	*
	* @return object
	*/
	function first() {
		return $this->item(0);
	}
	
	/**
	* Return the last item
	*
	* @return object
	*/
	function last() {
		return $this->item( count($this->DOMNodeList) -1 );
	}
	
	/**
	* Shortcut method for the item method
	*/
	function nth( $i ) {
		return $this->item($i);
	}
	
}

?>