# PipelineDeals API PHP Adapter

Lightweight multi-paradigm PHP (JSON) client for the [PipelineDeals API](http://www.pipelinedeals.com/api/).

## Requirements

* PHP 4 with [cURL support](http://php.net/manual/en/book.curl.php).


## Getting Started

Basic needs for authorization and redirecting

```php
<?php

	require 'pipelinedeals.php';

	$pdc = new PipelineDealsClient($api_key);
	
?>
```
