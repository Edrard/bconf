<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class AddType extends AbstractMigration
{
    protected function up(): void
    {
        $date = date("Y-m-d H:i:s");

        if($this->select("SELECT * FROM `type` WHERE type = 'router';") === []){
            $this->insert('connect',[
                'type' => 'router',
                'description' => 'Router',
                'created_at' => $date,
                'updated_at' => $date,
                ]
            );
        }
        if($this->select("SELECT * FROM `type` WHERE type = 'switch';") === []){
            $this->insert('connect',[
                'type' => 'switch',
                'description' => 'Switch',
                'created_at' => $date,
                'updated_at' => $date,
                ]
            );
        }
    }

    protected function down(): void
    {
        $this->delete('type',['type' => 'router']);
        $this->delete('type',['type' => 'switch']);
    }
}
