<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

class MigrationEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'postGenerateSchema',
        ];
    }

    /**
     * @param GenerateSchemaEventArgs $Args
     * @throws SchemaException
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $Args): void
    {
        $Schema = $Args->getSchema();

        if (!$Schema->hasNamespace('public')) {
            $Schema->createNamespace('public');
        }

        if (!$Schema->hasNamespace('topology')) {
            $Schema->createNamespace('topology');
        }

        if (!$Schema->hasNamespace('tiger')) {
            $Schema->createNamespace('tiger');
        }

        if (!$Schema->hasNamespace('tiger_data')) {
            $Schema->createNamespace('tiger_data');
        }
    }
}
