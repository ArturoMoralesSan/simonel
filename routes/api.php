<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\InventoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);

Route::get('/inventory', [InventoryController::class, 'getByProduct']);
Route::get('/inventory-clients', [InventoryController::class, 'getByClient']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/postal-code', function () {

    $postalCode = request('cp');

    if (!$postalCode) {

        return response()->json([
            'success' => false,
            'message' => 'Código postal requerido'
        ], 400);

    }

    $token = env('POSTALIA_API_KEY');

    $url = "https://postalia.com.mx/api/codigos-postales/{$postalCode}";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ])->get($url);

    if (!$response->successful()) {

        return response()->json([
            'success' => false,
            'message' => 'No se pudo obtener la información',
            'status' => $response->status(),
            'error' => $response->body()
        ], 500);

    }

    $data = $response->json();

    return response()->json([
        'success' => true,
        'codigo_postal' => $data['codigo_postal'] ?? null,
        'estado' => $data['estado'] ?? null,
        'municipio' => $data['municipio'] ?? null,
        'ciudad' => $data['ciudad'] ?? null,
        'zona' => $data['zona'] ?? null,
        'colonias' => collect($data['colonias'] ?? [])->pluck('nombre')
    ]);

});
