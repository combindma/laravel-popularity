# A Laravel package for tracking popular entries(by Models) of a website in a certain time

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/laravel-popularity.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-popularity)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/combindma/laravel-popularity/run-tests?label=tests)](https://github.com/combindma/laravel-popularity/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/combindma/laravel-popularity/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/combindma/laravel-popularity/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/laravel-popularity.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-popularity)

With Laravel Popularity Package you can Track your most popular Eloquent Models based on unique hits in a time range and then sort by popularity in a time frame.With Laravel Popular Package you can Track your most popular Eloquent Models based on unique hits in a time range and then sort by popularity in a time frame.

## Installation

You can install the package via composer:

```bash
composer require combindma/laravel-popularity
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="popularity-migrations"
php artisan migrate
```

## Usage

Use the visitable trait on the model you intend to track
```php
use Combindma\Popularity\Traits\Visitable;

class Page extends Model
{
    use Visitable;

    ...
}
```

This is how you create a visit (visits are not duplicated for a daily timeframe):
```php
// Adding a visit to the page.
$page->visit();

// Adding a visit to the page with ip address.
$page->visit()->withIp();

// Adding a visit with the given ip address.
$page->visit()->withIp('my-ip-address');

// Adding a visit with custom data.
$page->visit()->withData(['item' => 'value']);

// Adding a visit with logged user.
$page->visit()->withUser();

// Adding a visit with the given user.
$page->visit()->withUser($userId);

// Adding a visit with all above methods.
$page->visit()->withIp()->withUser()->withData(['item' => 'value']);
```

Changing the timeframe for creating visits with the same data:
```php
// creates visits after hourly timeframe
$page->hourlyInterval()->withIp();

// creates visits after a daily timeframe
$page->dailyInterval()->withIp();

// creates visits after a weekly timeframe
$page->weeklyInterval()->withIp();

// creates visits after a monthly timeframe
$page->monthlyInterval()->withIp();
```

Getting the records by the most visited
```php
// gets the total visit count
$page = Page::withTotalVisitCount()->first();
return $page->visit_count_total;

// gets records by all time popularity
$pages = Page::popularAlltime()->get();
return $pages->first()->visit_count_total;

// gets popular records between two dates
$pages = Page::popularBetween(Carbon::createFromDate(1990, 12, 01), Carbon::createFromDate(1990, 12, 04))->get();
return $pages->first()->visit_count;

// gets popular records by the last x days
$pages = Page::popularLastDays(2)->get();
retutn $pages->first()->visit_count;

// gets popular records by the last week
$pages = Page::popularLastWeek()->get();
return $pages->first()->visit_count;

// gets popular records by this week
$pages = Page::popularThisWeek()->get();
return $pages->first()->visit_count;

// gets popular records by the last month
$pages = Page::popularLastMonth()->get();
return $pages->first()->visit_count;

// gets popular records by this month
$pages = Page::popularThisMonth()->get();
return $pages->first()->visit_count;

// gets popular records by this year
$pages = Page::popularThisYear()->get();
return $pages->first()->visit_count;
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
