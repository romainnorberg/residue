# Residue

Divide a float into several parts, with distribution of any remainder.

#### Introduction

This dependency-free package provides a `split` method to help you split float into parts, with the possible distribution of any remainder.

It is also possible to specify a rounding of the divided amount, for example rounding by 0.05

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
$residue = (new Residue(100))->divideBy(3)->split(); // -> 33.33, 33.33, 33.34
```

#### Split with rounding (and remainder)

```php
$residue = (new Residue(100))
            ->divideBy(3)
            ->step(0.05)
            ->split(); // -> 33.35, 33.35, 33.30
```

With remainder:
```php
$residue = (new Residue(7.315))
            ->divideBy(3)
            ->decimal(3)
            ->step(0.05)
            ->split(); // -> [2.45, 2.45, 2.40]

...

$residue->getStepRemainder(); // -> 0.015
```

#### Generator

This package uses [generator](https://www.php.net/manual/en/language.generators.syntax.php) to reduce the memory used

With foreach statement (using generator):
```php
$residue = (new Residue(100))->divideBy(3);
foreach ($residue->split() as $part) {
    var_dump($part);
}

float(33.33)
float(33.33)
float(33.34)
```

To array:
```php
$residue = (new Residue(100))->divideBy(3);
var_dump($residue->toArray());

array(3) {
  [0]=>
  float(33.33)
  [1]=>
  float(33.33)
  [2]=>
  float(33.34)
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

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
