<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class EmailAddress
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        public readonly ?string $address = null
    ) {
        if (null !== $address) {
            $this->setAddress($address);
        }
    }

    private function setAddress(string $address): void
    {
        // Some validation logic here
        $this->address = $address;
    }
}
