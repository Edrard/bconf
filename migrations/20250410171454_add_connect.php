<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class AddConnect extends AbstractMigration
{
    protected function up(): void
    {
        $date = date("Y-m-d H:i:s");

        if($this->select("SELECT * FROM `connect` WHERE connect = 'ssh';") === []){
            $this->insert('connect',[
                'connect' => 'ssh',
                'description' => 'Ssh',
                'created_at' => $date,
                'updated_at' => $date,
                ]
            );
        }
        if($this->select("SELECT * FROM `connect` WHERE connect = 'telnet';") === []){
            $this->insert('connect',[
                'connect' => 'telnet',
                'description' => 'Telnet',
                'created_at' => $date,
                'updated_at' => $date,
                ]
            );
        }
    }

    protected function down(): void
    {
        $this->delete('connect',['connect' => 'ssh']);
        $this->delete('connect',['connect' => 'telnet']);
    }
}
