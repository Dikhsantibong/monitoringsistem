<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterMaterialController extends Controller
{
    public function index()
    {
        return view('admin.material-master.index');
    }
}
