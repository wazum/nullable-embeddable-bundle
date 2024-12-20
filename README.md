# Nullable Embeddable Bundle

[![CI](https://github.com/wazum/nullable-embeddable-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/wazum/nullable-embeddable-bundle/actions/workflows/ci.yml)

A [Symfony bundle](https://symfony.com/doc/current/bundles.html) that provides support for nullable [_Doctrine Embeddables_](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/tutorials/embeddables.html). It allows embeddable objects to become `null` when all their properties are `null`, providing a cleaner way to handle optional embedded values.

## Installation

Install the bundle using Composer:

```bash
composer require wazum/nullable-embeddable-bundle
```

The bundle supports:
- PHP 8.1 or higher
- Symfony 6.4 or 7.0 or higher
- Doctrine ORM 2.14 or 3.0 or higher

## Usage

### 1. Create your embeddable class

Create a standard Doctrine embeddable class:

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class EmailAddress
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public readonly ?string $address = null;

    // ... constructor and methods
}
```

### 2. Set up the containing entity

Add the `#[ContainsNullableEmbeddable]` attribute to your entity and mark the embeddable properties you want to be nullable with the `#[NullableEmbedded]` attribute:

```php
use Wazum\NullableEmbeddableBundle\Attribute\ContainsNullableEmbeddable;
use Wazum\NullableEmbeddableBundle\Attribute\NullableEmbedded;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ContainsNullableEmbeddable]
class User
{
    #[ORM\Embedded(class: EmailAddress::class)]
    #[NullableEmbedded]
    private ?EmailAddress $emailAddress = null;

    // ... constructor and methods
}
```

### 3. Configuration

The bundle will automatically register its Doctrine event subscriber. No additional configuration is required.

## How It Works

The bundle uses a [Doctrine event subscriber](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#reference-events-post-load) that listens to the `postLoad` event. When an entity marked with `#[ContainsNullableEmbeddable]` is loaded, the subscriber checks its properties marked with `#[NullableEmbedded]`. If an embeddable property has all its values set to `null`, the entire embeddable is set to `null`.

> The `postLoad` event occurs after the entity has been loaded into the current EntityManager from the database or after `refresh()` has been applied to it.

### Example

```php
$user = new User(
    emailAddress: new EmailAddress(address: null)
);

$entityManager->persist($user);
$entityManager->flush();
$entityManager->clear();

// When reloading:
$user = $entityManager->find(User::class, $user->getId());
$user->getEmailAddress(); // Returns null, not an EmailAddress instance with null values
```

## Features

- Zero configuration required: Just add the attributes and it works
- Automatically converts empty embeddable objects to null during database loads (instead of keeping objects with all-null properties)
- Fine-grained control: Mark only the properties you want to be nullable
- Works with any [Doctrine Embeddable](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/tutorials/embeddables.html)
- Supports both PHP attribute and XML mapping configurations
- Type-safe through PHP 8 features

## Troubleshooting
### Common Issues

- Embeddable not becoming `null`:
  - Verify the entity has the `#[ContainsNullableEmbeddable]` attribute
  - Verify the property has the `#[NullableEmbedded]` attribute
  - Check that all properties in the embeddable are actually `null`

- Type errors:
  - Ensure your embeddable properties are nullable (`?type`)
  - Ensure your entity's embeddable property is nullable

## Testing

Run the test suite:

```bash
composer test
```

Run all checks (CS Fixer, Psalm, and PHPUnit):

```bash
composer check-all
```

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
