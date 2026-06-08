<?php
namespace Database\Seeders;

use DB;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate(['permissions']);

        foreach ($this->getData() as $keyName => $name) {
            DB::table('permissions')->insert([
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
             /*
             * Profile
             */
            'update.password' => 'Cambiar contraseña',
            'update.profile'  => 'Cambiar Perfil',

            /*
             * products
             */
            'view.products'   => 'Ver productos',
            'create.products' => 'Agregar productos',
            'edit.products'   => 'Editar productos',
            'delete.products' => 'Eliminar productos',

            /*
             * Categories
             */
            'view.categories'   => 'Ver categorias',
            'create.categories' => 'Agregar categorias',
            'edit.categories'   => 'Editar categorias',
            'delete.categories' => 'Eliminar categorias',

            /*
             * payments
             */
            'view.orders'   => 'Ver ordenes',
            'create.orders' => 'Agregar ordenes',
            'edit.orders'   => 'Editar ordenes',
            'delete.orders' => 'Eliminar ordenes',


            /*
             * payments
             */
            'view.quotations'   => 'Ver cotizaciones',
            'create.quotations' => 'Agregar cotizaciones',
            'edit.quotations'   => 'Editar cotizaciones',
            'delete.quotations' => 'Eliminar cotizaciones',


            /*
             * pagos
             */
            'view.payments'   => 'Ver tipos de pago',
            'create.payments' => 'asignar tipo de pago',
            'edit.payments'   => 'Editar tipos de pago',
            'delete.payments' => 'Eliminar tipos de pago',

            /*
             * permission
             */
            'view.permissions'   => 'Ver permisos',
            'create.permissions' => 'Agregar permisos',
            'edit.permissions'   => 'Editar permisos',
            'delete.permissions' => 'Eliminar permisos',

            /*
             * roles
             */
            'view.roles'   => 'Ver roles',
            'create.roles' => 'Agregar roles',
            'edit.roles'   => 'Editar roles',
            'delete.roles' => 'Eliminar roles',

            /*
             * Users
             */
            'view.users'   => 'Ver Usuarios',
            'create.users' => 'Agregar usuarios',

            /*
             * Statistics
             */
            'view.statistics'   => 'Ver estadísticas',
            
        ];
    }
}
