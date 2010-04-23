<?php

/**
* @class Spider
* @author Robert McLeod <hamstar@telescum.co.nz>
* @date 23/04/2010
* @version 0.1b
* @copyright 2009 Robert McLeod
*/
class Spider {

	protected $d;
	protected $c;
	protected $body;
	protected $head;
	protected $returnCustomDOMNodeList = true;

	function __construct() {
		$this->d = new DOMDocument;
		$this->c = new Curl;
	}

	/**
	* Does a curl request using the curl library
	* and returns this.  Saves the head and
	* and body to the object. Loads the body
	* into the DOMDocument
	*
	* @param string $method POST or GET
	* @param string $url The url to request
	* @param array $vars Associative array of post data
	*
	* @return object
	*/
	function request( $method, $url, $vars=array() ) {
		$r = ( $method == 'POST' ) ?
			$this->c->post( $url, $vars ):
			$this->c->get( $url );
		
		if ( $r == false ) {
			throw new Exception('Curl Error: '. $this->c->error());
		}
		
		$this->body = $r->body;
		$this->head = $r->headers;
		
		@$this->d->loadHTML( $r->body );
		
		return $this;
	}
	
	/**
	* Shortcut method for a get request
	*
	* @param string $url The url to get
	*
	* @return object
	*/
	function get( $url ) {
		return $this->request( 'GET', $url );
	}
	
	/**
	* Shortcut method for a post request
	*
	* @param string $url The url to post
	* @param array $vars The post data
	*
	* @return object
	*/
	function post( $url, $vars ) {
		return $this->request( 'POST', $url, $vars );
	}

	/**
	* Main function for executing xpath queries
	* The source argument can be a url, html string
	* an array containing url to post to and array of
	* post data.
	*
	* @param string $patt The XPath query string
	* @param mixed $src The url to get, html to use, or array as above
	*
	* @return object
	*/
	private function xpath( $patt, $src ) {

		if( substr( $src, 0, 4 ) == 'http' ) {
			$src = $this->get( $src );
		} elseif ( is_array( $src ) ) {
			$src = $this->post( $src[0], $src[1]);
		} elseif ( !is_null( $src ) ) {
			$src = $src;
		}
		
		$x = new DOMXPath( $this->d );
		$DOMNodeList = $x->query( $patt );
		
		if ( $DOMNodeList->length == 0 ) {
			throw new Exception("Xpath query does not return any results: <pre>$patt</pre>");
		}
		
		if ( $this->returnCustomDOMNodeList ) {
			return new DOMNodeListWrapper( $DOMNodeList );
		}
		
		return $DOMNodeList;
	
	}
	
	/**
	* Shortcut method for xpath.  Returns an array containing
	* the nodeValues for each DOMNode object returned from the
	* xpath method.
	*
	* @param string $patt The xpath query
	* @param mixed $src The source to run the query on
	*
	* @return object
	*/
	function qa( $patt, $src=null ) {
		$objList = $this->xpath( $patt, $src );
		
		$a = array();
		foreach ( $objList() as $o ) {
			$a[] = $o->nodeValue;
		}
		
		return $a;
	}
	
	/**
	* Shortcut method for xpath.  Returns DOMNodeList
	*
	* @param string $patt The xpath query
	* @param mixed $src The source to run the query on
	*
	* @return object
	*/
	function qq( $patt, $src=null ) {
		return $this->xpath( $patt, $src );
	}
	
	/**
	* Shortcut method for xpath.  Returns the first object
	* from the DOMNodeList returned by the xpath method
	*
	* @param string $patt The xpath query
	* @param mixed $src The source to run the query on
	* @param int $i The list index to return
	*
	* @return object
	*/
	function qf( $patt, $src=null, $i=0 ) {
		return $this->xpath( $patt, $src )->item($i);
	}
	
	/**
	* Returns the headers from the last request, or if a
	* parameter name is provided, returns that instead.
	*
	* @param string $param The parameter to get
	*
	* @return mixed
	*/
	function getHead( $param=null ) {
		if ( is_null( $param ) ) {
			return $this->head;
		} else {
			return $this->head[$param];
		}
	}
	
	/**
	* Returns the raw HTML from the last request
	*
	* @return string
	*/
	function getBody() {
		return $this->body;
	}
	
	/**
	* Sets the curl options via an associative array.
	* Option names can be specified as done on github.com/shuber/curl
	* or php.net/curl_setopt
	*
	* @param array $options Associative array of options
	*
	* @return object
	*/
	function setCurlOptions( $options = array() ) {
		foreach ( $options as $n => $v ) {
			$this->c->headers[$n] = $v;
		}
		return $this;
	}
	
	/**
	* Sets the referer in curl
	*
	* @param string $r The referer
	*
	* @return object
	*/
	function setReferer( $r ) {
		$this->c->referer = $r;
		return $this;
	}
	
	/**
	* Sets the useragent in curl
	*
	* @param string $ua User agent string to use
	*
	* @return object
	*/
	function setUserAgent( $ua='no one interesting...' ) {
		$this->c->user_agent = $ua;
		return $this;
	}
	
	/**
	* Sets whether or not curl should follow redirects
	*
	* @param bool $b True or false
	*
	* @return object
	*/
	function followRedirects( $b ) {
		$this->c->options['followlocation'] = $b;
		return $this;
	}

	/**
	* Sets whether to return a custom DOMNodeList using
	* the DOMNodeListWrapper class, or a normal DOMNodeList
	*
	* @param bool $b True or false
	*
	* @return object
	*/
	function returnCustomDOMNodeList( $b ) {
		$this->returnCustomDOMNodeList = $b;
	}
	
}

?>