<?php

use App\Http\Controllers\Site\MainController;
use App\Http\Middleware\RedirectToAdminPanel;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'index'])->withoutMiddleware([EncryptCookies::class])->name('index')->middleware([RedirectToAdminPanel::class]);
Route::get('/preview/{appUuid}', [MainController::class, 'preview'])->name('preview')->withoutMiddleware([EncryptCookies::class, VerifyCsrfToken::class]);
Route::post('/preview', [MainController::class, 'previewPost'])->name('previewPost')->withoutMiddleware([EncryptCookies::class, VerifyCsrfToken::class]);
Route::match(['post', 'get'], '/postback', [MainController::class, 'postback'])->withoutMiddleware([EncryptCookies::class, VerifyCsrfToken::class])->name('postback');
Route::get('/acc', [MainController::class, 'acc'])->name('acc');
Route::get('/manifest', [MainController::class, 'manifest'])->name('manifest');
Route::match(['post', 'get'],'/analytic', [MainController::class, 'analytic'])->name('analytic')->withoutMiddleware([EncryptCookies::class, VerifyCsrfToken::class]);
Route::get('/go', [MainController::class, 'go'])->name('go')->withoutMiddleware([EncryptCookies::class]);
