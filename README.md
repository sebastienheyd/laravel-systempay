# Systempay form generator for Laravel

![Package](https://img.shields.io/badge/Package-sebastienheyd%2Flaravel-systempay.svg)
![Laravel](https://img.shields.io/badge/For-Laravel%20%E2%89%A5%205.7-lightgrey.svg)
![MIT License](https://img.shields.io/github/license/restoore/laravel-systempay.svg)

## Features

* Fast and easy form generation for Systempay (by Banque Populaire)
* Support multiple site id for multiple stores within the same project
* Support sha1 and hmac-sha-256
* Blade extension for more flexibility

## Installation

1. Install the package

```
composer require sebastienheyd/laravel-systempay
```

2. Publish the config file

```
php artisan vendor:publish --provider="Sebastienheyd\Systempay\SystempayServiceProvider"
```

## Configuration

After publishing edit the default configuration file : [`config/systempay.php`](src/config/systempay.php)

```php
return [
    'default' => [
        'site_id' => 'YOUR_SITE_ID',
        'key'     => env('SYSTEMPAY_SITE_KEY', 'YOUR_KEY'),
        'env'     => env('SYSTEMPAY_ENV', 'PRODUCTION'),
    ]
];
```

You need to set `YOUR_SITE_ID` and `YOUR_KEY` with your own values. This two values are given by Systempay.

### Hashing algorithms

By default, the package will use hmac-sha-256 to generate the signature. To use sha1 you need to set `algo` to `sha1`
in the configuration :

```php
return [
    'default' => [
        // ...
        'algo'    => 'sha1'
    ]
];
```

### Specific parameters

These parameters are set by default :

| name | default value | note |
|---|---|---|
| currency | 978 | [List of currency codes](https://www.iban.com/currency-codes) | 
| payment_config | SINGLE | SINGLE or MULTIPLE |
| trans_date | [current datetime] | Generated automaticaly |
| page_action | PAYMENT |  |
| action_mode | INTERACTIVE |  |
| version | V2 |  |
| signature | [generated] | Generated automaticaly |

Also see [Systempay documentation](https://paiement.systempay.fr/doc/fr-FR/form-payment/quick-start-guide/envoyer-un-formulaire-de-paiement-en-post.html)

**NB** : you don't have to add the `vads_` prefix to parameters, the prefix will be automaticaly added. 
But you can also set the parameters with the `vads_` prefix, it will be automaticaly removed.

There is also possible to set some specific parameters to a configuration by setting `params` values.

Example :

```php
return [
    'default' => [
        // ...
        'params'  => [
            'currency' => '826'
        ]
    ]
];
```

In this case, default configuration will use the currency code 826.

### Additional configuration

You can add as many configuration as you need by adding a new key to the configuration file.

For example :

```php
return [
    'default' => [
       // ...
    ],
    'store_uk' => [
        'site_id' => '123456',
        'key'     => env('SYSTEMPAY_UK_SITE_KEY', '12345678'),
        'env'     => env('SYSTEMPAY_UK_ENV', 'PRODUCTION'),
        'algo'    => 'sha256'        
    ]
];
```

To use another configuration, call the `config` method, for example :

```php 
$systemPay = Systempay::config('store_uk')->set([
    'amount' => 12.34,
    'trans_id' => 123456
]);
```

## Usage

In your controller :

```php
<?php namespace App\Http\Controllers;

use Systempay; // Facade

class PaymentController extends Controller
{
    public function payment()
    {
        $systemPay = Systempay::set([
            'amount' => 12.34,
            'trans_id' => 123456
        ]);
        
        return view('payment', compact('systemPay'));
    }
}
```

In your view

```blade
{!! $systemPay->render('<button type="submit" class="btn">Payment</button') !!}
```

Or with the Blade extension :

```blade
@systempay
    <button class="btn btn-lg btn-success">Payment</button>
@endsystempay
```

**NB** : With the Blade extension, if your variable name passed to the view is not `$systemPay` you need to 
set it like this :

 ```blade
 @systempay('paymentData')
 ```
 
 In this example it will use `$paymentData` instead of `$systemPay`