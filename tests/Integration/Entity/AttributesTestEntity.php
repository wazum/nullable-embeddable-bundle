<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wazum\NullableEmbeddableBundle\Attribute\ContainsNullableEmbeddable;

#[ORM\Entity]
#[ContainsNullableEmbeddable]
class AttributesTestEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: EmailAddress::class)]
    private ?EmailAddress $emailAddress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailAddress(): ?EmailAddress
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?EmailAddress $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }
}
