<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\EmbeddedClassMapping;
use Wazum\NullableEmbeddableBundle\ContainsNullableEmbeddable;
use Wazum\NullableEmbeddableBundle\NullableEmbeddable;

#[AsDoctrineListener(event: Events::postLoad)]
final class NullableEmbeddableSubscriber implements EventSubscriber
{
    /**
     * @return array<int, string>
     *
     * @psalm-return array{0: 'postLoad'}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
        ];
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof ContainsNullableEmbeddable) {
            return;
        }

        $metadata = $args->getObjectManager()->getClassMetadata($entity::class);
        $this->processEmbeddables($entity, $metadata->embeddedClasses);
    }

    /**
     * @param array<string, EmbeddedClassMapping> $embeddedClasses
     *
     * @throws \ReflectionException
     */
    private function processEmbeddables(ContainsNullableEmbeddable $entity, array $embeddedClasses): void
    {
        /** @var string $fieldName */
        foreach (array_keys($embeddedClasses) as $fieldName) {
            $property = new \ReflectionProperty($entity, $fieldName);
            /** @var ?NullableEmbeddable $value */
            $value = $property->getValue($entity);

            if ($value instanceof NullableEmbeddable && $this->allPropertiesAreNull($value)) {
                $property->setValue($entity, null);
            }
        }
    }

    private function allPropertiesAreNull(NullableEmbeddable $object): bool
    {
        foreach ((new \ReflectionObject($object))->getProperties() as $property) {
            if (null !== $property->getValue($object)) {
                return false;
            }
        }

        return true;
    }
}
