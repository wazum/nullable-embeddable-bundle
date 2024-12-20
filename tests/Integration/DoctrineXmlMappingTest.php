<?php

declare(strict_types=1);

namespace Wazum\NullableEmbeddableBundle\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Wazum\NullableEmbeddableBundle\Doctrine\NullableEmbeddableSubscriber;
use Wazum\NullableEmbeddableBundle\Tests\Integration\Entity\EmailAddress;
use Wazum\NullableEmbeddableBundle\Tests\Integration\Entity\XmlTestEntity;

class DoctrineXmlMappingTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private NullableEmbeddableSubscriber $subscriber;

    protected function setUp(): void
    {
        $config = ORMSetup::createXMLMetadataConfiguration(
            paths: [__DIR__.'/config/xml'],
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ], $config);

        $this->entityManager = new EntityManager($connection, $config);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);

        $this->subscriber = new NullableEmbeddableSubscriber();
        $this->entityManager->getEventManager()->addEventSubscriber($this->subscriber);
    }

    public function testEmbeddableBecomesNullWhenEmpty(): void
    {
        $entity = new XmlTestEntity();
        $embeddable = new EmailAddress(address: null);
        $entity->setEmailAddress($embeddable);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->entityManager->clear();
        $reloadedEntity = $this->entityManager->find(XmlTestEntity::class, $entity->getId());

        $this->assertNull($reloadedEntity->getEmailAddress());
    }
}
