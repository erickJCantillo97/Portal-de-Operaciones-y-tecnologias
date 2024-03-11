<?php

use App\Http\Controllers\GD\ManagerDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('getTipologias/{project}', [ManagerDocumentController::class, 'index'])->name('get.tipologias');

Route::get('getProjectsGD', [ManagerDocumentController::class, 'getProjectsGD'])->name('get.projectsGD');

Route::post('gestionDocumentalStore', [ManagerDocumentController::class, 'store'])->name('gestion.documental.store');

Route::get('getFilesProjectTipologia/{projectID}/{tipologiaID}', [ManagerDocumentController::class, 'getFilesProjectTipologia'])->name('get.files.project.tipologia');
