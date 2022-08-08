<?php

use Toproplus\Export\Http\Controllers\ExportController;

Route::get('export', ExportController::class.'@index');