<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class AddTimestamp extends AbstractMigration
{
    protected function up(): void
    {

        $set['default'] = NULL;
        $set['null'] = TRUE;

        $this->table('group', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('group', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();
        $this->table('devices_config', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('devices_config', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();
        $this->table('type', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('type', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();
        $this->table('connect', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('connect', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();
        $this->table('model', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('model', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();
    }

    protected function down(): void
    {
        $this->execute('ALTER TABLE `connect` DROP `created_at`;');
        $this->execute('ALTER TABLE `model` DROP `created_at`;');
        $this->execute('ALTER TABLE `type` DROP `created_at`;');
        $this->execute('ALTER TABLE `group` DROP `created_at`;');
        $this->execute('ALTER TABLE `devices_config` DROP `created_at`;');
        $this->execute('ALTER TABLE `connect` DROP `updated_at`;');
        $this->execute('ALTER TABLE `model` DROP `updated_at`;');
        $this->execute('ALTER TABLE `type` DROP `updated_at`;');
        $this->execute('ALTER TABLE `group` DROP `updated_at`;');
        $this->execute('ALTER TABLE `devices_config` DROP `updated_at`;');
    }
}
