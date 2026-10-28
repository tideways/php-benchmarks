<?php

use App\Http\Controllers\UserList;
use Illuminate\Support\Facades\Route;

Route::get('/', UserList::class);
