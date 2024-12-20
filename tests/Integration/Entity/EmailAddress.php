<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wazum\NullableEmbeddableBundle\NullableEmbeddable;

#[ORM\Embeddable]
final class EmailAddress implements NullableEmbeddable
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    public function __construct(?string $address = null)
    {
        if (null !== $address) {
            $this->setAddress($address);
        }
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    private function setAddress(string $address): void
    {
        $this->address = $address;
    }
}
