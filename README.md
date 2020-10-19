# Residue ⚖️

<p class="rich-diff-level-zero" align="left">
        <a href="https://packagist.org/packages/romainnorberg/residue" rel="nofollow" class="rich-diff-level-one"><img src="https://img.shields.io/packagist/v/romainnorberg/residue.svg?style=flat-square" alt="Latest Version on Packagist" style="max-width:100%;"></a>
        <a href="https://github.com/romainnorberg/residue/actions?query=workflow%3Arun-tests+branch%3Amaster" class="rich-diff-level-one"><img src="https://img.shields.io/github/workflow/status/romainnorberg/residue/run-tests?label=tests" alt="GitHub Tests Action Status" style="max-width:100%;"></a>
    <a href="https://codecov.io/gh/romainnorberg/residue" rel="nofollow" class="rich-diff-level-one"><img src="https://codecov.io/gh/romainnorberg/residue/branch/master/graph/badge.svg" alt="codecov" style="max-width:100%;"></a>
    <a href="https://packagist.org/packages/romainnorberg/residue/stats" rel="nofollow" class="rich-diff-level-one"><img src="https://img.shields.io/packagist/dt/romainnorberg/residue.svg?style=flat-square" alt="Total Downloads" style="max-width:100%;"></a></p>

### Divide a float into several parts, with distribution of any remainder.
    
<img src="https://raw.githubusercontent.com/romainnorberg/residue/master/.github/media/readme.jpg" align="center" alt="Residue Package - Illustration credit: https://refactoring.guru/" width="100%">

#### Introduction

This dependency-free package provides a `split` method to help you split float into parts, with the possible distribution of any remainder.

It is also possible to specify a rounding of the divided amount, for example rounding by 0.05.

**[🕹 Try out Residue on the online tester! »](https://residue-online-php-tester.herokuapp.com/)**

#### Installation

You can install the package via composer:

```bash
composer req romainnorberg/residue
```

#### Requirements

This package require >= PHP 7.4

#### Residue VS Brick\Money

This package does not deal with the notion of currency and being more basic, it is up to 40 times faster on basic operations than the [brick/money](https://github.com/brick/money) package

Benchmarks: [residue-vs-brick-money](https://github.com/romainnorberg/residue-vs-brick-money)

## Usage / examples

#### Basic split

```php
Residue::create(100)->divideBy(3)->split(); // -> \Generator[33.34, 33.33, 33.33]

// or

Residue::create(100)->divideBy(3)->toArray(); // -> [33.34, 33.33, 33.33]
```

#### Split with rounding (and remainder)

```php
Residue::create(100)
            ->divideBy(3)
            ->step(0.05)
            ->split(); // -> \Generator[33.35, 33.35, 33.30]
```

With remainder:

```php
$r = Residue::create(7.315)
                ->divideBy(3)
                ->step(0.05);

$r->split(); // -> \Generator[2.45, 2.45, 2.40]
$r->getRemainder(); // -> 0.015
```

#### Split mode

`SPLIT_MODE_ALLOCATE` is default mode and try to allocate the maximum of the value according to step.

```php
$r = Residue::create(100)
            ->divideBy(3)
            ->decimal(0);

$r->split(); // -> \Generator[34, 33, 33]
$r->getRemainder(); // 0

//

$r = Residue::create(101)
            ->divideBy(3)
            ->decimal(0);

$r->split(); // -> \Generator[34, 34, 33]
$r->getRemainder(); // 0
```

`SPLIT_MODE_EQUITY` mode try to allocate equally the maximum of the value according to step.

```php
$r = Residue::create(100)
            ->divideBy(3)
            ->decimal(0);

$r->split(Residue::SPLIT_MODE_EQUITY); // -> \Generator[33, 33, 33]
$r->getRemainder(); // 1

//

$r = Residue::create(101)
            ->divideBy(3)
            ->decimal(0);

$r->split(Residue::SPLIT_MODE_EQUITY); // -> \Generator[33, 33, 33]
$r->getRemainder(); // 2
```

#### Generator

This package uses [generator](https://www.php.net/manual/en/language.generators.syntax.php) to reduce the memory used

With foreach statement (using generator):
```php
$r = Residue::create(100)->divideBy(3);
foreach ($r->split() as $part) {
    var_dump($part);
}

float(33.34)
float(33.33)
float(33.33)
```

To array:
```php
$r = Residue::create(100)->divideBy(3);
var_dump($r->toArray());

array(3) {
  [0]=>
  float(33.34)
  [1]=>
  float(33.33)
  [2]=>
  float(33.33)
}
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email romainnorberg@gmail.com instead of using the issue tracker.

## Credits

- [Romain Norberg](https://github.com/romainnorberg)
- [All Contributors](../../contributors)
- Illustration from Refactoring.Guru https://refactoring.guru/

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
