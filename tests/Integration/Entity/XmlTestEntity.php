<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Integration\Entity;

use Wazum\NullableEmbeddableBundle\Attribute\ContainsNullableEmbeddable;
use Wazum\NullableEmbeddableBundle\Attribute\NullableEmbedded;

#[ContainsNullableEmbeddable]
class XmlTestEntity
{
    private ?int $id = null;
    #[NullableEmbedded]
    private $emailAddress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }
}
