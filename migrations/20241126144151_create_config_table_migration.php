<?php

declare(strict_types=1);

namespace Migration;

use Phoenix\Migration\AbstractMigration;

final class CreateConfigTableMigration extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('config', 'id')
        ->setCharset('utf8mb4')
        ->setCollation('utf8mb4_general_ci')
        ->addColumn('id', 'integer', ['autoincrement' => true])
        ->addColumn('name', 'string')
        ->addColumn('value', 'string')
        ->addIndex('name', 'unique', 'btree', 'key')
        ->create();

        $set['default'] = NULL;
        $set['null'] = TRUE;

        $this->table('config', 'id')
        ->addColumn('created_at', 'timestamp',$set)
        ->save();
        $this->table('config', 'id')
        ->addColumn('updated_at', 'timestamp',$set)
        ->save();

        $date = date("Y-m-d H:i:s");

        $this->insert('config',[
            'name' => 'override',
            'value' => '0',
            'created_at' => $date,
            'updated_at' => $date,
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'disable|dumping',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'disable|saving',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'save|path',
            'value' => 'Dumps'
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'main|retries',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'main|retries_timeout',
            'value' => '10'
            ]
        );
        $this->insert('config',[
            'created_at' => $date,
            'updated_at' => $date,
            'name' => 'main|disable_logs_send',
            'value' => '3'
            ]
        );
    }

    protected function down(): void
    {
        $this->table('config')
        ->drop();
    }
}
