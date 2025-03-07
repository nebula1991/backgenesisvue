<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function all()
    {
        return response()->json(Subcategory::get());
    }
    
    public function index()
    {
        $subcategories = Subcategory::with('category')->paginate(10);
        return response()->json($subcategories);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',           // Obligatorio, cadena, máximo 255 caracteres
            'description' => 'nullable|string|max:100',   // Opcional, cadena, máximo 1000 caracteres
            'category_id' => 'required|exists:categories,id', // Obligatorio, debe existir en la tabla categories
            
        ]);
        return response()->json(Subcategory::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json([
                'message' => 'Subcategoria no encontrada'
            ], 404);
        }
        return response()->json($subcategory);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        $subcategory->update($request->all());
        return response()->json($subcategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return response()->json('ok');
    }
}
