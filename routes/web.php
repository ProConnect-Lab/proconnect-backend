<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('docs.ui');
});

Route::view('/admin/{view?}', 'admin')
    ->where('view', '.*')
    ->name('admin.app');

Route::get('/docs/openapi', function () {
    $path = base_path('docs/openapi.yaml');

    abort_unless(file_exists($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/yaml',
    ]);
})->name('docs.openapi');

Route::view('/docs', 'docs.swagger')->name('docs.ui');
