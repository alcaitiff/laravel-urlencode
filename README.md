# Laravel 5 Url Encode

Overrides the default routing in Laravel 5 to allow all characters to be encoded including slashes!

This project is an adaptation of [https://github.com/Artistan/Urlencode](https://github.com/Artistan/Urlencode) allowing this fix to work in Laravel 5

## Composer configuration

Include the artistan urlencode package as a dependency in your `composer.json` [Packagist](https://packagist.org/packages/alcaitiff/laravel-urlencode):

```bash
    "alcaitiff/laravel-urlencode:"1.*"
```

Or execute the command

```bash
    composer require alcaitiff/laravel-urlencode:"1.*"
```

### Installation

Once you update your composer configuration, run `composer install` to download the dependencies.

Add a ServiceProvider to your providers array in `app/config/app.php`:

```php
  'providers' => [
    ...
    Alcaitiff\LaravelUrlEncode\RouteServiceProvider::class,
    ...
  ];
```

Add a method on your App\Http\Kernel to allow the router substitution

```php
  //This is needed because Laravel DO NOT use his own service provider system
  //You can't change the app router and the kernel router through the dependency injection
  //The framework set a router at the very beginning of the stack and do not allow changes
  //We can only hope in future implementations actually using the injection system allowing that
  public function setRouter(Router $router) {
    $this->router = $router;
    return $this;
  }
```

### Apache conf [AllowEncodedSlashes](http://httpd.apache.org/docs/2.2/mod/core.html#allowencodedslashes)

```bash
  AllowEncodedSlashes On|NoDecode
```

### Warning

Ensure all your routes are properly rawurlencoded!

This package will actually break your routing IF you do not have valid urls in your routes.

### Laravel Bug Fix

[[Bug] urlencoded slashes in routing parameters](https://github.com/laravel/framework/pull/4338)
Current routes do not allow for urlencoded slashes in the paths.
This is problematic when trying to create ecommerce solutions with partnumbers in the routes
since many part numbers have slashes in them. There are also quite a few
manufacturers with slashes in their names and or brands. This package provides
the functionality to allow an uri to have encoded slashes, and other characters,
in the [routes](http://laravel.com/docs/routing) and also to
[create routes with those parameters](http://laravel.com/docs/routing#route-parameters).

An example url may be...

```php
  //https://stage.test.com/part/Cisco%20Systems%2C%20Inc/CISCO2851-SRST%2FK9
    Route::any('/part/{mfg}/{part}',
        array(
            'uses' =>'Vendorname\Package\Controllers\Hardware\PartController@part',
            'as' => 'part_page'
        )
    );
```

### Usage

With that said it WILL allow all characters to be rawurlencoded as parameters in your routes without breaking parameters and/or routes.
