Water Level Scraping using PHP
==============

[![Build Status](https://img.shields.io/travis/katsana/my-waterlevel/master.svg?style=flat)](https://travis-ci.org/katsana/my-waterlevel)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/katsana/my-waterlevel/master.svg?style=flat)](https://scrutinizer-ci.com/g/katsana/my-waterlevel/)

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"katsana/my-waterlevel": "dev-master"
	},
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/katsana/my-waterlevel.git"
		}
	]
}
```

And then run `composer install` from the terminal.

## Usage

```php
<?php

use MyKatsana\WaterLevel\Providers\InfoBanjir\Client;

$client = new Client;
$response = $client->executeByState('WLH');
```

`$response` will return a collection (array) of `MyKatsana\WaterLevel\Providers\InfoBanjir\Data`.