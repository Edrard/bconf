<?php

declare(strict_types=1);

namespace Migration;

use Phoenix\Migration\AbstractMigration;

final class AddDiscriptions extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('group', 'id')
        ->addColumn('description', 'string')
        ->save();
        $this->table('devices_config', 'id')
        ->addColumn('description', 'string')
        ->save();
        $this->table('type', 'id')
        ->addColumn('description', 'string')
        ->save();
    }

    protected function down(): void
    {
        $this->execute('ALTER TABLE `type` DROP `description`;');
        $this->execute('ALTER TABLE `group` DROP `description`;');
        $this->execute('ALTER TABLE `devices_config` DROP `description`;');
    }
}
