<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\EmbeddedClassMapping;
use Wazum\NullableEmbeddableBundle\Attribute\ContainsNullableEmbeddable;
use Wazum\NullableEmbeddableBundle\Attribute\NullableEmbedded;

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

    /**
     * @throws \ReflectionException
     */
    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!(new \ReflectionClass($entity))->getAttributes(ContainsNullableEmbeddable::class)) {
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
    private function processEmbeddables(object $entity, array $embeddedClasses): void
    {
        /** @var string $fieldName */
        foreach (array_keys($embeddedClasses) as $fieldName) {
            $property = new \ReflectionProperty($entity, $fieldName);
            if (!$property->getAttributes(NullableEmbedded::class)) {
                continue;
            }

            /** @var object|null $value */
            $value = $property->getValue($entity);
            if (null === $value) {
                continue;
            }
            if ($this->allPropertiesAreNull($value)) {
                $property->setValue($entity, null);
            }
        }
    }

    private function allPropertiesAreNull(object $object): bool
    {
        foreach ((new \ReflectionObject($object))->getProperties() as $property) {
            if (null !== $property->getValue($object)) {
                return false;
            }
        }

        return true;
    }
}
