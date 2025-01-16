# PHP Typed

> `Typed` is a lightweight PHP utility for seamless type-casting and data retrieval from dynamic variables, arrays, and
> objects.

This package provides a single `Typed` class with static methods and offers compatibility with PHP versions `7.4+` and
`8.0+`.

## 1. Why Use Typed?

Handling type casting in PHP often leads to verbose and repetitive constructions, especially in array-related cases.

`Typed` streamlines this process,
allowing you to fetch and cast values with concise, readable code.

**Example: Plain PHP**

```php
function getTypedIntFromArray(array $array): int
{
    return true === isset($array['meta']['number']) &&
             true === is_numeric($array['meta']['number'])
             ? (int)$array['meta']['number']
             : 0;
}

function getTypedStringFromMixedVariable($mixed): string
{
    return true === is_string($mixed) || 
    true === is_numeric($mixed)
        ? (string)$mixed
        : '';
}
```

**The same with the `Typed` utility**

```php
use function WPLake\Typed\int;
use function WPLake\Typed\string;

function getTypedIntFromArray(array $data): int
{
    return int($data, 'meta.number');
}

function getTypedStringFromMixedVariable($mixedData): string
{
    return string($mixedData);
}
```

The code like `string($array, 'key')` resembles `(string)$array['key']` while being
safe and smart — it even handles nested keys.

> In case now you're thinking: "Hold on guys, but this code won't work! Are your using type names as function names?" 
> 
> Our answer is: "Yes! And actually it isn't prohibited."
> 
> See the explanation in the special section - [5. Note about the function names](#5-note-about-the-function-names)

Backing to the package. Want to provide a default value when the key is missing? Here you go:

```php
string($data, 'some.key', 'Default Value');
```

Don't like functions? The same functions set is available as static methods of the `Typed` class:

```php
use WPLake\Typed\Typed;

Typed::int($data,'key');
```

## 2. Installation and usage

Typed class is distributed as a Composer package, making installation straightforward:

`composer require wplake/typed`

After installation, ensure that your application includes the Composer autoloader (if it hasn’t been included already):

`require __DIR__ . '/vendor/autoload.php';`

Usage:

```php
use function WPLake\Typed\string;
use WPLake\Typed\Typed;

$string = string($array, 'first.second','default value');
// alternatively:
$string = Typed::string($array, 'first.second','default value');
```

## 3. Supported types

Functions for the following types are present:

* `string`
* `int`
* `float`
* `bool`
* `array`
* `object`
* `dateTime`
* `any` (allows to use short dot-keys usage for unknowns)

Additionally:

* `boolExtended` (`true`,`1`,`"1"`, `"on"` are treated as true, `false`,`0`,`"0"`, `"off"` as false)
* `stringExtended` (supports objects with `__toString`)

For optional cases, each item has an `OrNull` method option (e.g. `stringOrNull`, `intOrNull`, and so on),
which returns `null` if the key doesn’t exist.

## 4. How It Works

The logic of all casting methods follows this simple principle:

> “Provide me a value of the requested type from the given source by the given path, or return the default value.”

For example, let's review the `string` method declaration:

```php
namespace WPLake\Typed;

/**
 * @param mixed $source
 * @param int|string|array<int,int|string>|null $keys
 */
function string($source, $keys = null, string $default = ''): string;
```

Usage Scenarios:

1. Extract a string from a mixed variable (returning the default if absent or of an incompatible type)

```php
$userName = string($unknownVar);
```

2. Retrieve a string from an array, including nested structures (with dot notation or as an array).

```php
$userName = string($array, 'user.name');
// alternatively:
$userName = string($array, ['user','name',]);
```

3. Access a string from an object. It also supports the nested properties.

```php
$userName = string($companyObject, 'user.name');
// alternatively:
$userName = string($companyObject, ['user', 'name',]);
```

4. Work with mixed structures (e.g., `object->arrayProperty['key']->anotherProperty or ['key' => $object]`).

```php
$userName = string($companyObject,'users.john.name');
// alternatively:
$userName = string($companyObject,['users','john','name',]);
```

In all the cases, you can pass a default value as the third argument, e.g.:

```php
$userName = string($companyObject,'users.john.name', 'Guest');
```

## 5. Note about the function names

Surprisingly, PHP allows functions to use the same names as variable types.

Think it’s prohibited? Not quite! While certain names are restricted for classes, interfaces, and traits, function names
are not:

> “These names cannot be used to name a **class, interface, or
> trait**” - [PHP Manual: Reserved Other Reserved Words](https://www.php.net/manual/en/reserved.other-reserved-words.php)

This means you we can have things like `string($array, 'key')`, which resembles `(string)$array['key']` while being
safer
and smarter — it even handles nested keys.

Note: Unlike all the other types, the `array` keyword falls under
a [different category](https://www.php.net/manual/en/reserved.keywords.php), which also prohibits its usage for function
names. That's why in this case we used the `arr` name instead.

## 6. FAQ

### 6.1) Why not just straight type casting?

Straight type casting in PHP can be unsafe and unpredictable in certain scenarios.

For example, the following code will throw an error if the `$mixed` variable is an object of a class that doesn’t
explicitly implement `__toString`:

```php
class Example {
// ...
}
$mixed = new Example();
// ...
function getName($mixed):void{
 return (string)$mixed;
}
```

Additionally, attempting to cast an array to a string, like `(string)$myArray` will:

1. Produce a PHP Notice: Array to string conversion.
2. Return the string "Array", which is rarely the intended behavior.

This unpredictability can lead to unexpected bugs and unreliable code.

### 6.2) Why not just Null Coalescing Operator?

While the Null Coalescing Operator (`??`) is useful, it doesn’t address type checking or casting requirements.

```php
// Plain PHP:
$number = $data['meta']['number']?? 10;
$number = true === is_numeric($number)?
(int)$number:
10;

// Typed:
$number = Typed::int($data, 'meta.number', 10);
```

Additionally, with Null Coalescing Operator and a custom default value, you have to repeat yourself.

### 6.3) Shouldn't we use typed objects instead?

OOP is indeed powerful, and you should always prioritize using objects whenever possible. However, the reality is that
our code often interacts with external dependencies beyond our control.

This package simplifies handling such scenarios.
Any seasoned PHP developer knows the pain of type-casting when working with environments outside of frameworks, e.g. in
WordPress.

### 6.4) Is the dot syntax in keys inspired by Laravel Collections?

Yes, the dot syntax is inspired by [Laravel Collections](https://laravel.com/docs/11.x/collections) and similar
solutions. It provides an intuitive way to access
nested data structures.

### 6.5) Why not just use Laravel Collections?

Laravel Collections and similar libraries don’t offer type-specific methods like this package does.

While extending
[Laravel Collections package](https://github.com/illuminate/collections) could be a theoretical solution, we opted for a
standalone package because:

1. **PHP Version Requirements:** Laravel Collections require PHP 8.2+, while Typed supports PHP 7.4+.
2. **Dependencies:** Laravel Collections bring several external Laravel-specific dependencies.
3. **Global Functions:** Laravel Collections rely on global helper functions, which are difficult to scope when needed.

In addition, when we only need to extract a single variable, requiring the entire array to be wrapped in a collection
would be excessive.

## 7. Contribution

We would be excited if you decide to contribute 🤝

Please open Pull Requests against the `main` branch.

### Code Style Agreements:

#### 7.1) PSR-12 Compliance

Use the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) tool in your IDE with the provided `phpcs.xml`,
or run `composer phpcbf` to format your code.

#### 7.2) Static Analysis with PHPStan

Set up [PHPStan](https://phpstan.org/) in your IDE with the provided `phpstan.neon`, or run `composer phpstan` to
validate your code.

#### 7.3) Unit Tests

[Pest](https://pestphp.com/) is setup for Unit tests. Run them using `composer pest`.