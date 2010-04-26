# Spider

## What is this?
This is a class inspired by simplehtmldom and rubys Hpricot to scrape websites in a single line of code.

## What do I require?
You need Sean Hubers excellent [curl library](/shuber/curl) and the two DOM wrappers here.

## How do I use it?
### Initialization
First init it like so:
	include 'globals.inc';
	$s = new Spider;
	
## Getting html
Then you can get your html
	$s->get('http://example.com');
	$s->post('http://example.com', array('some field' => 'some value'));

You can access the raw HTML by using:
	$s->getBody();
	
And you can access the headers like so:
	$allHeadersArray = $s->getHead();
	
	$statusCode = $s->getHead('Status-code');
	
## Extracting elements
Then you can extract elements using xpath in one of three ways

### The get-the-nodeValues-of-each-node-and-give-me-them-in-an-array way
This will return the nodevalues of each matching node in an array.
	$array = $s->qa('//div/a'); // or use //div/a/@href to get all the links
	print_r($array);
	
	/* Outputs array of each nodes nodeValue property
	Array
	(
		[0] => Daybreakers (2009)
		[1] => Daybreakers - Wikipedia, the free encyclopedia
		[2] => DAYBREAKERS - ON BLU-RAY, DVD and DIGITAL DOWNLOAD
		[3] => YouTube - Daybreakers - Official Trailer [HD]
		[4] => YouTube - 'Daybreakers' Trailer HD
		[5] => Daybreakers Movie Reviews, Pictures - Rotten Tomatoes
		[6] => Daybreakers - Movie Trailers - iTunes
		[7] => Daybreakers review - Story - Entertainment - 3 News
		[8] => Daybreakers trailers and video clips on Yahoo! Movies
		[9] => Daybreakers Movie Synopsis and Overview
		[10] => News results for daybreakers
	)
	*/

### The just-give-me-the-first-node-there-is-only-one-anyway way
This method is for those times when you know you only want the first node found.
	// Drops the first matching nodes href attribute
	echo $s->qf('//div/a')->href;
	
### The full DOMNodeList way
This way is closer to the normal return from a DOMXPath::query() call.  Except the nodes are wrapped in a custom DOMNode wrapper
inside a custom DOMNodeList wrapper giving it a bit more functionality
	$list = $s->qq('//div/a');
	
	// Easy selectors
	echo $list->first()->href;
	echo $list->last()->href;
	echo $list->nth(5)->href;
	echo $list(5)->href;
	
	// Add and remove nodes
	$list->addNode( $domNode );
	$list->removeNode(5);

	// Put them through a foreach
	foreach( $list() as $node ) {
		echo $node->title;
	}

## The one liner way
You could use it like this
	$array = $s->get('http://example.com/')->qa('//div/a');
	
But you can also do this
	$array = $s->qa('//div/a', 'http://example.com');
	$link = $s->qf('//div/a', 'http://example.com')->href;
	$list = $s->qq('//div/a', 'http://example.com');
	
And with post too
	$array = $s->qa('//div/a', array('http://example.com', $postData));
	$link = $s->qf('//div/a', array('http://example.com', $postData))->href;
	$list = $s->qq('//div/a', array('http://example.com', $postData));
	
## Extra DOMNode Functionality
The extra DOMNode functionality is just the ability to access attributes easily.  I can't stand
calling getAttribute() on nodes, it should be able to pick up any attribute used
	$node->inner;
	$node->plain;
	$node->href;
	$node->src;
	// ...et al
	
You can also access the original DOMNode like so
	$node->n->nodeValue;

You can choose not to use the custom wrappers by setting the config option for it.

## The Templated Way
There is also an option to apply a template to a webpage and return an object with the fields you want already extracted and formatted.  So one does not need to make find all the XPaths for a site if someone has made a template.
	include 'template.IMDB.php';
	
	$s = new Spider;
	$imdb = new IMDBTemplate;
	
	//$movie = $s->get( $url )->applyTemplate( $imdb );
	$movie = $s->applyTemplate( $imdb, $url );
	
	print_r( $movie );

This should output something like:
	stdClass Object
	(
		[title] => Daybreakers
		[year] => 2009
		[rating] => 6.6/10
		[votes] => 15000
	)

This part however has not been tested.

## Configuration
These methods should be self explanatory
	$s->returnCustomDOMNodeList( false ); // use unwrapped DOMNodeLists
	$s->setReferer('http://example.com/');
	$s->setUserAgent('PHP Spider/0.1');
	$s->followRedirects(true);
	
This method can be used to send curl options (case/prefix insensitive)
	$opts = array(
		'CURLOPT_PORT' = 8080
	//	'curlopt_port' = 8080
	//	'port' = 8080
	//	'PORT' = 8080
	);
	
	$s->setCurlOptions( $opts );

## Making your own Template
You can make your own template by creating a php script with the following format:
	class SiteTemplate {
		public $someField = '//xpath/goes/here';
		//... et al
		
		// The apply template function will pass the found fields back
		// to this function as an object with the same fields as defined above
		function process( $o ) {
			$o->someField = strip_tags( $o->someField );
			//...et al
			return $o;
		}
	}

The Spider class will load the template, extract what it can using the XPaths, put all the fields and values into and object and pass it back to the templates process method.  This allows one to modify the fields from within the template in order to strip tags, white space, extract numbers... anything you can do with a PHP function.

Process then returns the modified object back to the applyTemplate method of the Spider, which gives the object back to where from you called it.

If there are no changes to be made to any fields then the templates process method needs to return the object passed to it.