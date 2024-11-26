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
        $this->insert('config',[
            'name' => 'override',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'disable|dumping',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'disable|saving',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'save|path',
            'value' => 'Dumps'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|file|dst',
            'value' => 'logs'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|file|full',
            'value' => '1'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|file|disable',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|file|debug',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|file|per_run',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|user',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|pass',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|smtp',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|port',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|from',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|to',
            'value' => ''
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|separate',
            'value' => '0'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|only_important',
            'value' => '1'
            ]
        );
        $this->insert('config',[
            'name' => 'logs|mail|subject',
            'value' => 'My Server'
            ]
        );
    }

    protected function down(): void
    {
        $this->table('config')
        ->drop();
    }
}
