<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Wazum\NullableEmbeddableBundle\Attribute\ContainsNullableEmbeddable;
use Wazum\NullableEmbeddableBundle\Attribute\NullableEmbedded;
use Wazum\NullableEmbeddableBundle\Doctrine\NullableEmbeddableSubscriber;

class NullableEmbeddableSubscriberTest extends TestCase
{
    private NullableEmbeddableSubscriber $subscriber;
    private EntityManagerInterface $entityManager;
    private ClassMetadata $metadata;

    protected function setUp(): void
    {
        $this->subscriber = new NullableEmbeddableSubscriber();
        $this->metadata = new ClassMetadata('TestEntity');
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testPostLoadWithNonNullableEntity(): void
    {
        $entity = new class {
        };

        $args = new PostLoadEventArgs($entity, $this->entityManager);

        $this->subscriber->postLoad($args);

        // Test passes if no error occurs
        $this->assertTrue(true);
    }

    public function testPostLoadWithNullableEntity(): void
    {
        $embeddable = new class {
            private ?string $value = null;
        };

        $entity = new #[ContainsNullableEmbeddable] class {
            #[ORM\Embedded(class: 'SomeClass')]
            private ?object $embeddable;

            public function getEmbeddable(): ?object
            {
                return $this->embeddable;
            }

            public function setEmbeddable(?object $embeddable): void
            {
                $this->embeddable = $embeddable;
            }
        };

        $this->metadata->embeddedClasses = [
            'embeddable' => [
                'class' => $embeddable::class,
                'columnPrefix' => null,
            ],
        ];
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($this->metadata);

        $entity->setEmbeddable($embeddable);

        $args = new PostLoadEventArgs($entity, $this->entityManager);

        $this->subscriber->postLoad($args);

        $this->assertNull($entity->getEmbeddable());
    }

    public function testPostLoadWithNoTypeHint(): void
    {
        $embeddable = new class {
            private ?string $value = null;
        };

        $entity = new #[ContainsNullableEmbeddable] class {
            #[ORM\Embedded(class: 'SomeClass')]
            #[NullableEmbedded]
            private $embeddable;

            public function getEmbeddable()
            {
                return $this->embeddable;
            }

            public function setEmbeddable($embeddable): void
            {
                $this->embeddable = $embeddable;
            }
        };

        $this->metadata->embeddedClasses = [
            'embeddable' => [
                'class' => $embeddable::class,
                'columnPrefix' => null,
            ],
        ];

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($this->metadata);

        $entity->setEmbeddable($embeddable);

        $args = new PostLoadEventArgs($entity, $this->entityManager);
        $this->subscriber->postLoad($args);

        $this->assertNull($entity->getEmbeddable());
    }
}
