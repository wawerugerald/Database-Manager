<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DatabaseInstance;

class DatabaseInstancesSeeder extends Seeder
{
    public function run()
    {
        DatabaseInstance::create([
            'name' => 'Local MySQL',
            'type' => 'mysql',
            'project' => 'local_mysql',
            'compose_path' => base_path('docker/mysql/docker-compose.yml'),
            'env' => [],
            'status' => 'stopped'
        ]);

        DatabaseInstance::create([
            'name' => 'Local Postgres',
            'type' => 'postgres',
            'project' => 'local_postgres',
            'compose_path' => base_path('docker/postgres/docker-compose.yml'),
            'env' => [],
            'status' => 'stopped'
        ]);

        DatabaseInstance::create([
            'name' => 'Local MongoDB',
            'type' => 'mongodb',
            'project' => 'local_mongo',
            'compose_path' => base_path('docker/mongodb/docker-compose.yml'),
            'env' => [],
            'status' => 'stopped'
        ]);
    }
}
