<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class AddModels extends AbstractMigration
{
    protected function up(): void
    {
        $date = date("Y-m-d H:i:s");
        $this->insert('model',[
            'model' => 'cisco',
            'description' => 'Cisco',
            'created_at' => $date,
            'updated_at' => $date,
            ]
        );
        $this->insert('model',[
            'model' => 'mikrotik',
            'description' => 'Mikrotik',
            'created_at' => $date,
            'updated_at' => $date,
            ]
        );
        $this->insert('model',[
            'model' => 'mikrotikkeys',
            'description' => 'Mikrotik with show-sensitive',
            'created_at' => $date,
            'updated_at' => $date,
            ]
        );
    }

    protected function down(): void
    {
        $this->delete('model',['model' => 'cisco']);
        $this->delete('model',['model' => 'mikrotik']);
        $this->delete('model',['model' => 'mikrotikkeys']);
    }
}
