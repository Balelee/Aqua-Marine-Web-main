<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\deliveryAdress;
use App\Http\Controllers\Controller;

class DeliveryAdressController extends Controller
{

    public function store(Request $request)
{
    // Validation des champs
    $validated = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'name'     => 'required|string|max:255',
        'phone'    => 'required|string|max:20',
        'email'    => 'required|email|max:255',
        'city'     => 'required|string|max:100',
        'district' => 'required|string|max:100',
    ]);
    $address = deliveryAdress::create($validated);
    return response()->json([
        'data' => $address
    ], 201);
}


}
