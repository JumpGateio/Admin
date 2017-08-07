<?php

use JumpGate\Core\Abstracts\Seeder;

class AdminRoles extends Seeder
{
    public function run()
    {
        $existingRole = $this->db
            ->table('rbac_roles')
            ->where('slug', 'admin')
            ->first();

        if (is_null($existingRole)) {
            $role = [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Admin user role.',
                'created_at'  => date('Y-m-d H:i:s'),
            ];

            $this->db->table('rbac_roles')->insert($role);
        }
    }
}
