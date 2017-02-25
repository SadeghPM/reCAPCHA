# PSR-7 Google reCAPCHA Middleware
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)<br>
This middleware implement server side google reCAPCHA v2 validation.It was originally developed for Slimframework 3 but can be used with any framework using PSR-7 style middlewares.
It is very simple and without any dependancy.

## Install

Install latest version using [composer](https://getcomposer.org/).

``` bash
$ composer require sadegh-pm/recapcha
```

## Usage
Add the middleware in the route that you rendering the reCAPTCHA widget. for example i inserted reCAPTCHA widget in the <code>/login</code> route:
```php
  $app->post('/login', App\Api\Login::class . ':verify')
      ->add( new \SadeghPM\Recapcha\GoogleReCapcha($reCAPCHA_Secret) );
```
if google reCAPCHA verifying the user's response , your route will be resolved otherwise app will be terminate.the terminated  response status code is <code>403</code> and body is a json:
```json
{
  "ok":false,
  "description":"errors..."
}
```
