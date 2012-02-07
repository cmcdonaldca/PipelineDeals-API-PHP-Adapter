# PipelineDeals API PHP Adapter

Lightweight multi-paradigm PHP (JSON) client for the [PipelineDeals API](http://www.pipelinedeals.com/api/).

## Requirements

* PHP 4 with [cURL support](http://php.net/manual/en/book.curl.php).

## TODO

Handle API calls that allow for Query String parameters.  Currently, not supported.

## Getting Started

First get your API KEY and pass it to the constructor.

Use the API documentation to see what methods you need GET, PUT, etc

Here is a basic example of the usage

```php
<?php

	require 'pipelinedeals.php';
	$api_key = 'PUT_YOUR_KEY_HERE';
	$pdc = new PipelineDealsClient($api_key);
	try {
		// get all people into array
		$people = $pdc->call('GET', 'people.json');

		// update a person's first_name
		$id = 123;
		$person = array("id"=>$id, "first_name"=>"Colin");
		$result = $pdc->call('PUT', "people/$id.json", array("person", $person));

	} catch(ClientApiException $ex) {
		// log error
	}

?>
```
