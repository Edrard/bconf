<?php

declare(strict_types=1);

namespace Migration;

use Phoenix\Migration\AbstractMigration;

final class AddDiscriptions extends AbstractMigration
{
    protected function up(): void
    {
        $set['default'] = '';

        $this->table('group', 'id')
        ->addColumn('description', 'string',$set)
        ->save();
        $this->table('devices_config', 'id')
        ->addColumn('description', 'string',$set)
        ->save();
        $this->table('type', 'id')
        ->addColumn('description', 'string',$set)
        ->save();
        $this->table('connect', 'id')
        ->addColumn('description', 'string',$set)
        ->save();
        $this->table('model', 'id')
        ->addColumn('description', 'string',$set)
        ->save();
    }

    protected function down(): void
    {
        $this->execute('ALTER TABLE `type` DROP `description`;');
        $this->execute('ALTER TABLE `group` DROP `description`;');
        $this->execute('ALTER TABLE `connect` DROP `description`;');
        $this->execute('ALTER TABLE `model` DROP `description`;');
        $this->execute('ALTER TABLE `devices_config` DROP `description`;');
    }
}
