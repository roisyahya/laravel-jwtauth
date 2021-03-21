<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ProductController extends Controller
{
    public function product() {
        $data = "All Products";
        return response()->json($data, 200);
    }

    public function productAuth() {
        $data = "Product Owner: " . Auth::user()->name;
        return response()->json($data, 200);
    }
}