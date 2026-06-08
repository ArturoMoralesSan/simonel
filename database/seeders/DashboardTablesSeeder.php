<?php

namespace Database\Seeders;

use App\Models\Permission;
use DB;

class DashboardTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate(['dashboard_sections', 'dashboard_submenus', 'dashboard_links']);

        $permissions = Permission::all('id', 'key_name')->pluck('id', 'key_name');

        foreach ($this->getData() as $i => $section) {
            $sectionId = DB::table('dashboard_sections')->insertGetId([
                'name'       => $section['name'],
                'tile'       => $section['tile'],
                'order'      => $i + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            foreach ($section['submenus'] as $j => $submenu) {
                $submenuId = DB::table('dashboard_submenus')->insertGetId([
                    'name'       => $submenu['name'],
                    'icon'       => $submenu['icon'],
                    'order'      => $j + 1,
                    'section_id' => $sectionId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                foreach ($submenu['links'] as $k => $link) {
                    DB::table('dashboard_links')->insert([
                        'name'          => $link['name'],
                        'route'         => $link['route'],
                        'order'         => $k + 1,
                        'submenu_id'    => $submenuId,
                        'permission_id' => $permissions[$link['permission']],
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }



    /**
     * Return the data to populate tables.
     *
     * @return array
     */
    private function getData()
    {
        return [
            [
                'name' => 'ACL',
                'tile' => 'lock.svg',
                'submenus' => [
                    [
                        'name' => 'Permisos',
                        'icon' => 'permissions.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar permisos',
                                'route'      => 'agregar-permisos',
                                'permission' => 'create.permissions'
                            ],
                            [
                                'name'       => 'Lista de permisos',
                                'route'      => 'permisos',
                                'permission' => 'view.permissions'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Roles',
                        'icon' => 'role.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar roles',
                                'route'      => 'agregar-roles',
                                'permission' => 'create.roles'
                            ],
                            [
                                'name'       => 'Lista de roles',
                                'route'      => 'roles',
                                'permission' => 'view.roles'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Usuarios',
                        'icon' => 'users.svg',
                        'links' => [
                            [
                                'name'       => 'Lista de usuarios',
                                'route'      => 'usuarios',
                                'permission' => 'view.users'
                            ]
                        ],
                    ],
                    
                ]
            ],
            [
                'name' => 'Productos',
                'tile' => 'productos.svg',
                'submenus' => [
                    
                    [
                        'name' => 'Categorías',
                        'icon' => 'categorias.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar categorias',
                                'route'      => 'agregar-categorias',
                                'permission' => 'create.categories'
                            ],
                            [
                                'name'       => 'Lista de categorias',
                                'route'      => 'categorias',
                                'permission' => 'view.categories'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Productos',
                        'icon' => 'productos.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar productos',
                                'route'      => 'agregar-productos',
                                'permission' => 'create.products'
                            ],
                            [
                                'name'       => 'Lista de productos',
                                'route'      => 'productos',
                                'permission' => 'view.products'
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Ventas',
                'tile' => 'orders.svg',
                'submenus' => [
                    [
                        'name' => 'Ordenes',
                        'icon' => 'orders.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar orden',
                                'route'      => 'agregar-orden',
                                'permission' => 'create.orders'
                            ],
                            [
                                'name'       => 'Lista de ordenes',
                                'route'      => 'ordenes',
                                'permission' => 'view.orders'
                            ],
                        ]
                    ],
                    [
                        'name' => 'cotizaciones',
                        'icon' => 'cotizaciones.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar cotización',
                                'route'      => 'agregar-cotizacion',
                                'permission' => 'create.quotations'
                            ],
                            [
                                'name'       => 'Lista de cotizaciones',
                                'route'      => 'cotizaciones',
                                'permission' => 'view.quotations'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tipo de pago',
                        'icon' => 'payments.svg',
                        'links' => [
                            [
                                'name'       => 'Agregar tipo de pago',
                                'route'      => 'agregar-tipo-pago',
                                'permission' => 'create.payments'
                            ],
                            [
                                'name'       => 'Lista de tipos de pago',
                                'route'      => 'tipos-pagos',
                                'permission' => 'view.payments'
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Indicadores',
                'tile' => 'indicadores.svg',
                'submenus' => [
                    [
                        'name' => 'Estadísticas',
                        'icon' => 'estadisticas.svg',
                        'links' => [
                            [
                                'name'       => 'Estadísticas de servicios',
                                'route'      => 'estadisticas-servicios',
                                'permission' => 'view.statistics'
                            ],
                            [
                                'name'       => 'Estadísticas de gastos',
                                'route'      => 'estadisticas-servicios',
                                'permission' => 'view.statistics'
                            ],
                        ]
                    ],
                ]
            ],
            
        ];
    }
}

