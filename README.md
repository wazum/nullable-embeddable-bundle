# Nullable Embeddable Bundle

A Symfony bundle that provides support for nullable Doctrine embeddables. It allows embeddable objects to become `null` when all their properties are `null`, providing a cleaner way to handle optional embedded values.

## Installation

Install the bundle using Composer:

```bash
composer require wazum/nullable-embeddable-bundle
```

The bundle supports:
- PHP 8.2 or higher
- Symfony 7.0 or higher
- Doctrine ORM 3.0 or higher

## Usage

### 1. Mark your embeddable class

Implement the `NullableEmbeddable` interface on your embeddable class:

```php
use Wazum\NullableEmbeddableBundle\NullableEmbeddable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class EmailAddress implements NullableEmbeddable
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    // ... getters and setters
}
```

### 2. Mark the containing entity

Implement the `ContainsNullableEmbeddable` interface on entities that use nullable embeddables:

```php
use Wazum\NullableEmbeddableBundle\ContainsNullableEmbeddable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User implements ContainsNullableEmbeddable
{
    #[ORM\Embedded(class: EmailAddress::class)]
    private ?EmailAddress $emailAddress = null;

    // ... getters and setters
}
```

### 3. Configuration

The bundle will automatically register its Doctrine event subscriber. No additional configuration is required.

## How It Works

The bundle uses a Doctrine event subscriber that listens to the `postLoad` event. When an entity implementing `ContainsNullableEmbeddable` is loaded, the subscriber checks all its embeddable properties. If an embeddable implements `NullableEmbeddable` and all its properties are `null`, the entire embeddable is set to `null`.

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

- Zero configuration required: Just implement the interfaces and it works
- Works with Doctrine ORM's embedded objects
- Supports both PHP attribute and XML mapping configurations
- Automatically handles null state conversion
- Type-safe through PHP 8.2+ features

## Troubleshooting
### Common Issues

- Embeddable not becoming `null`:
  - Verify the embeddable class implements `NullableEmbeddable`
  - Verify the entity implements `ContainsNullableEmbeddable`
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
