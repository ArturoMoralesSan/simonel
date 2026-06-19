<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\CutController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InventoryProductController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\MeasureController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\RaceController;
use App\Http\Controllers\Admin\CalculatorController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\LotController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ProductionOrderController;
use App\Http\Controllers\Admin\ProductLotController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ManufacturedProductController;

use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Auth::routes(['register' => false]);
Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('{provider}/callback', [LoginController::class,'handleProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('notas/{id}', [PdfController::class, 'pdfSale']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'noCache']], function() {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('pdf/{id}', [PdfController::class, 'pdf']);

    Route::get('pdf-carrera/{id}', [PdfController::class, 'pdfRace']);

    Route::get('pdf-egreso/{id}', [PdfController::class, 'pdfEgreso']);
    Route::get('pdf-gasto/{id}', [PdfController::class, 'pdfGasto']);
        
    Route::post('ultimo_rx/{id}', [ServiceController::class, 'getrx']);

    //perfil
    Route::view('perfil/editar','admin.editar-perfil');
    Route::put('perfil/editar', [ProfileController::class, 'update']);

    //Password
    Route::view('perfil/cambiar-contrasena', 'admin.cambiar-contrasena');
    Route::post('perfil/cambiar-contrasena', [PasswordController::class, 'update']);

    // usuarios
    Route::get('usuarios', [UserController::class, 'index']);
    Route::get('agregar-usuario', [UserController::class, 'create']);
    Route::post('usuarios/crear', [UserController::class, 'save']);
    Route::get('usuarios/{id}/editar',[UserController::class, 'edit']);
    Route::put('usuarios/{id}/actualizar',[UserController::class, 'update']);
    Route::delete('usuarios/eliminar/{id}',[UserController::class, 'destroy']);

    // clientes
    Route::get('clientes', [CustomerController::class, 'index']);
    Route::get('agregar-cliente', [CustomerController::class, 'create']);
    Route::post('clientes/crear', [CustomerController::class, 'save']);
    Route::get('clientes/{id}/editar',[CustomerController::class, 'edit']);
    Route::put('clientes/{id}/actualizar',[CustomerController::class, 'update']);
    Route::delete('clientes/eliminar/{id}',[CustomerController::class, 'destroy']);

    //permisos
    Route::get('permisos', [PermissionController::class, 'index']);
    Route::view('agregar-permisos', 'admin.permisos.crear');
    Route::post('permiso/crear', [PermissionController::class, 'save']);
    Route::get('permiso/{id}/editar', [PermissionController::class, 'edit']);
    Route::put('permiso/{id}/actualizar', [PermissionController::class, 'update']);
    Route::delete('permiso/eliminar/{id}', [PermissionController::class, 'delete']);

    //roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::get('agregar-roles', [RoleController::class, 'create']);
    Route::post('roles/crear', [RoleController::class, 'save']);
    Route::get('roles/{id}/editar', [RoleController::class, 'edit']);
    Route::put('roles/{id}/actualizar', [RoleController::class, 'update']);
    Route::delete('roles/eliminar/{id}', [RoleController::class, 'delete']);

    //proveedores
    Route::get('proveedores', [SupplierController::class, 'index']);
    Route::view('agregar-proveedores', 'admin.proveedores.crear');
    Route::post('proveedores/crear', [SupplierController::class, 'save']);
    Route::get('proveedores/{id}/editar', [SupplierController::class, 'edit']);
    Route::put('proveedores/{id}/actualizar', [SupplierController::class, 'update']);
    Route::delete('proveedores/eliminar/{id}', [SupplierController::class, 'delete']);

    Route::get('compras', [PurchaseController::class, 'index']);
    Route::get('compras/{id}/detalle', [PurchaseController::class, 'details']);
    Route::get('agregar-compras', [PurchaseController::class, 'create']);
    Route::post('compras/crear', [PurchaseController::class, 'save']);
    Route::get('compras/{id}/editar', [PurchaseController::class, 'edit']);
    Route::put('compras/{id}/actualizar', [PurchaseController::class, 'update']);
    Route::delete('compras/eliminar/{id}', [PurchaseController::class, 'delete']);

    //Categorias
    Route::get('categorias', [TypeController::class, 'index']);
    Route::view('agregar-categoria', 'admin.categorias.crear');
    Route::post('categorias/crear', [TypeController::class, 'save']);
    Route::get('categorias/{id}/editar', [TypeController::class, 'edit']);
    Route::put('categorias/{id}/actualizar', [TypeController::class, 'update']);
    Route::delete('categorias/eliminar/{id}', [TypeController::class, 'delete']);

    //Materia prima
    Route::get('materia-prima', [RawMaterialController::class, 'index']);
    Route::get('agregar-materia-prima', [RawMaterialController::class, 'create']);
    Route::post('materia-prima/crear', [RawMaterialController::class, 'save']);
    Route::get('materia-prima/{id}/editar', [RawMaterialController::class, 'edit']);
    Route::put('materia-prima/{id}/actualizar', [RawMaterialController::class, 'update']);
    Route::delete('materia-prima/eliminar/{id}', [RawMaterialController::class, 'delete']);

    //Almacenes
    Route::get('almacenes', [WarehouseController::class, 'index']);
    Route::get('agregar-almacenes', [WarehouseController::class, 'create']);
    Route::post('almacenes/crear', [WarehouseController::class, 'save']);
    Route::get('almacenes/{id}/editar', [WarehouseController::class, 'edit']);
    Route::put('almacenes/{id}/actualizar', [WarehouseController::class, 'update']);
    Route::delete('almacenes/eliminar/{id}', [WarehouseController::class, 'delete']);

    //catalogo de productos
    Route::get('catalogo', [ManufacturedProductController::class, 'index']);
    Route::get('agregar-catalogo', [ManufacturedProductController::class, 'create']);
    Route::post('catalogo/crear', [ManufacturedProductController::class, 'save']);
    Route::get('catalogo/{id}/editar', [ManufacturedProductController::class, 'edit']);
    Route::put('catalogo/{id}/actualizar', [ManufacturedProductController::class, 'update']);
    Route::delete('catalogo/eliminar/{id}', [ManufacturedProductController::class, 'delete']);


    //Lotes
    Route::get('lotes', [LotController::class, 'index']);
    Route::get('agregar-lotes', [LotController::class, 'create']);
    Route::post('lotes/crear', [LotController::class, 'save']);
    Route::get('lotes/{id}/editar', [LotController::class, 'edit']);
    Route::put('lotes/{id}/actualizar', [LotController::class, 'update']);
    Route::delete('lotes/eliminar/{id}', [LotController::class, 'delete']);

    //recetas
    Route::get('recetas', [RecipeController::class, 'index']);
    Route::get('agregar-recetas', [RecipeController::class, 'create']);
    Route::post('recetas/crear', [RecipeController::class, 'save']);
    Route::get('recetas/{id}/editar', [RecipeController::class, 'edit']);
    Route::put('recetas/{id}/actualizar', [RecipeController::class, 'update']);
    Route::delete('recetas/eliminar/{id}', [RecipeController::class, 'delete']);
    Route::get('recetas/{id}/detalle',[RecipeController::class, 'details']);

    // orden de producción
    Route::get('produccion', [ProductionOrderController::class, 'index']);
    Route::get('agregar-produccion', [ProductionOrderController::class, 'create']);
    Route::post('produccion/crear', [ProductionOrderController::class, 'save']);
    Route::get('produccion/{id}/editar', [ProductionOrderController::class, 'edit']);
    Route::put('produccion/{id}/actualizar', [ProductionOrderController::class, 'update']);
    Route::delete('produccion/eliminar/{id}', [ProductionOrderController::class, 'delete']);
    Route::get('produccion/{id}/detalle',[ProductionOrderController::class, 'details']);

    // Lote de producto terminado
    Route::get('lotes-producto', [ProductLotController::class, 'index']);
    Route::get('lotes-producto/{id}/detalle',[ProductLotController::class, 'details']);
    Route::get('lotes-producto/{id}/etiqueta',[ProductLotController::class, 'label']);
    
    //Medidas
    Route::get('medidas', [MeasureController::class, 'index']);
    Route::view('agregar-medidas', 'admin.medidas.crear');
    Route::post('medidas/crear', [MeasureController::class, 'save']);
    Route::get('medidas/{id}/editar', [MeasureController::class, 'edit']);
    Route::put('medidas/{id}/actualizar', [MeasureController::class, 'update']);
    Route::delete('medidas/eliminar/{id}', [MeasureController::class, 'delete']);

    //Productos
    Route::get('productos', [ProductController::class, 'index']);
    Route::get('agregar-productos', [ProductController::class, 'create']);
    Route::post('productos/clonar/{id}', [ProductController::class, 'cloneProduct']);
    Route::post('productos/crear', [ProductController::class, 'save']);
    Route::get('productos/{id}/editar', [ProductController::class, 'edit']);
    Route::put('productos/{id}/actualizar', [ProductController::class, 'update']);
    Route::delete('productos/eliminar/{id}', [ProductController::class, 'delete']);

    //inventario
    Route::get('inventario', [InventoryProductController::class, 'index']);
    Route::get('agregar-inventario', [InventoryProductController::class, 'create']);
    Route::post('inventario/crear', [InventoryProductController::class, 'save']);
    Route::get('inventario/{id}/detalle', [InventoryProductController::class, 'details']);
    Route::post('inventario-movimiento/{id}/actualizar', [InventoryProductController::class, 'updateMovement']);
    Route::get('inventario/{id}/editar', [InventoryProductController::class, 'edit']);
    Route::post('inventario/{id}/actualizar', [InventoryProductController::class, 'update']);
    Route::delete('inventario/eliminar/{id}', [InventoryProductController::class, 'delete']);

     //inventario clientes
    Route::get('inventario-clientes', [InventoryController::class, 'index']);
    Route::get('agregar-inventario-clientes', [InventoryController::class, 'create']);
    Route::post('inventario-clientes/crear', [InventoryController::class, 'storeMovement']);
    Route::get('inventario-clientes/{id}/detalle', [InventoryController::class, 'details']);
    Route::post('inventario-clientes-movimiento/{id}/actualizar', [InventoryController::class, 'updateMovement']);
    Route::get('inventario-clientes/{id}/editar', [InventoryController::class, 'edit']);
    Route::post('inventario-clientes/{id}/actualizar', [InventoryController::class, 'update']);
    Route::delete('inventario-clientes/eliminar/{id}', [InventoryController::class, 'delete']);

    //Ventas
    Route::get('ventas', [SaleController::class, 'index']);
    Route::get('ventas/orden/{id}', [SaleController::class, 'order']);
    Route::put('ventas/orden/{id}/actualizar', [SaleController::class, 'orderupdate']);
    Route::post('ventas/clonar/{id}', [SaleController::class, 'cloneSale']);

    Route::get('agregar-venta', [SaleController::class, 'create']);
    Route::post('ventas/crear', [SaleController::class, 'save']);
    Route::get('ventas/{id}/editar', [SaleController::class, 'edit']);
    Route::put('ventas/{id}/actualizar', [SaleController::class, 'update']);
    Route::delete('ventas/eliminar/{id}', [SaleController::class, 'delete']);

    //Acabados
    Route::get('acabados', [CutController::class, 'index']);
    Route::view('agregar-acabado', 'admin.acabados.crear');
    Route::post('acabados/crear', [CutController::class, 'save']);
    Route::get('acabados/{id}/editar', [CutController::class, 'edit']);
    Route::put('acabados/{id}/actualizar', [CutController::class, 'update']);
    Route::delete('acabados/eliminar/{id}', [CutController::class, 'delete']);

    //Tipo de pago
    Route::get('tipos-pagos', [PaymentController::class, 'index']);
    Route::view('agregar-tipo-pago', 'admin.pagos.crear');
    Route::post('tipos-pagos/crear', [PaymentController::class, 'save']);
    Route::get('tipos-pagos/{id}/editar', [PaymentController::class, 'edit']);
    Route::put('tipos-pagos/{id}/actualizar', [PaymentController::class, 'update']);
    Route::delete('tipos-pagos/eliminar/{id}', [PaymentController::class, 'delete']);

    //Tipo de gastos
    Route::get('tipos-gastos', [TypeExpenseController::class, 'index']);
    Route::view('agregar-tipos-gastos', 'admin.tipogastos.crear');
    Route::post('tipos-gastos/crear', [TypeExpenseController::class, 'save']);
    Route::get('tipos-gastos/{id}/editar', [TypeExpenseController::class, 'edit']);
    Route::put('tipos-gastos/{id}/actualizar', [TypeExpenseController::class, 'update']);
    Route::delete('tipos-gastos/eliminar/{id}', [TypeExpenseController::class, 'delete']);

    //Estudios
    Route::get('estudios', [StudyController::class, 'index']);
    Route::view('agregar-estudios', 'admin.estudios.crear');
    Route::post('estudios/crear', [StudyController::class, 'save']);
    Route::get('estudios/{id}/editar', [StudyController::class, 'edit']);
    Route::put('estudios/{id}/actualizar', [StudyController::class, 'update']);
    Route::delete('estudios/eliminar/{id}', [StudyController::class, 'delete']);
    
    // Estadísticas
    Route::get('estadisticas-servicios', [StatisticsController::class, 'index']);

    Route::get('estadisticas-gastos', [StatisticsController::class, 'gastos']);

    //Usuarios
    Route::get('usuarios', [UserController::class, 'index']);

    //Password
    Route::view('cambiar-contrasena', 'principal.cambiar-contrasena');
    Route::post('cambiar-contrasena', 'Auth\PasswordController@update');
});