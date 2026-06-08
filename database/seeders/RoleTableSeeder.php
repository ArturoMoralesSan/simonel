<?php
namespace Database\Seeders;

use DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate(['roles']);

        foreach ($this->getData() as $keyName => $name) {
            DB::table('roles')->insert([
                'key_name'   => $keyName,
                'name'       => $name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }


    /**
     * Return the data to populate table.
     *
     * @return array
     */
    private function getData()
    {
        return [
            'superadmin'  => 'Super Administrador',
            'customer'    => 'Cliente',
            'employee'    => 'Empleado',
        ];
    }
}

