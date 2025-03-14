<?php

declare(strict_types=1);

namespace Migration;

use Phoenix\Database\Element\Index;
use Phoenix\Migration\AbstractMigration;

final class CreateDeviceTableMigration extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('connect', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('connect', 'string')
            ->addIndex('connect', 'unique', 'btree', 'connect')
            ->create();

        $this->table('devices_config', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('date', 'datetime')
            ->addColumn('name', 'string')
            ->addColumn('ip', 'string', ['length' => 39])
            ->addColumn('port', 'mediuminteger', ['length' => 6])
            ->addColumn('login', 'string')
            ->addColumn('password', 'string')
            ->addColumn('group_id', 'integer')
            ->addColumn('type_id', 'integer')
            ->addColumn('connect_id', 'integer')
            ->addColumn('model_id', 'integer')
            ->addColumn('config_enable', 'tinyinteger', ['length' => 1, 'comment' => 'Can be 0 - disabled, 0 - enabled'])
            ->addColumn('config_enable_command', 'string', ['default' => ''])
            ->addColumn('config_enable_pass', 'string', ['default' => ''])
            ->addColumn('config_enable_pass_str', 'string', ['default' => ''])
            ->addColumn('config_search', 'text')
            ->addColumn('status', 'boolean', ['default' => true, 'comment' => '0 - disabled device, 1- enabled device'])
            ->addIndex('name', 'unique', 'btree', 'name')
            ->addIndex('group_id', '', 'btree', 'group')
            ->addIndex('type_id', '', 'btree', 'type')
            ->addIndex('model_id', '', 'btree', 'model')
            ->addIndex('connect_id', '', 'btree', 'connect')
            ->addIndex('status', '', 'btree', 'status')
            ->create();

        $this->table('group', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('group', 'string')
            ->addIndex('group', 'unique', 'btree', 'group')
            ->create();

        $this->table('model', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('model', 'string')
            ->addIndex('model', 'unique', 'btree', 'model')
            ->create();

        $this->table('type', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('type', 'string')
            ->addIndex('type', 'unique', 'btree', 'type')
            ->create();
    }

    protected function down(): void
    {
        $this->table('connect')
            ->drop();

        $this->table('devices_config')
            ->drop();

        $this->table('group')
            ->drop();

        $this->table('model')
            ->drop();

        $this->table('type')
            ->drop();
    }
}
