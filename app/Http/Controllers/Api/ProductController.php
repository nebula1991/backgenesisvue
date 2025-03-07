<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function all()
    {
        $products = Product::all();
        return response()->json($products);
    }
    
    public function index()
    {
        return response()->json(Product::with('category','subcategory')->paginate(10));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',           // Obligatorio, cadena, máximo 255 caracteres
            'description' => 'nullable|string|max:100',   // Opcional, cadena, máximo 1000 caracteres
            'price' => 'required|numeric|min:0',           // Obligatorio, numérico, mínimo 0
            'stock' => 'required|integer|min:0',           // Obligatorio, entero, mínimo 0
            'category_id' => 'required|exists:categories,id', // Obligatorio, debe existir en la tabla categories
            'subcategory_id' => 'nullable|exists:subcategories,id', // Opcional, debe existir si se proporciona
        ]);
        
        return response()->json(Product::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json('ok');
    }

    public function upload(Request $request, Product $product){

        $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:10240'
        ]);

        Storage::disk('public_upload')->delete("image/".$product->image);

        $data['image'] = $filename = time() . '.' . $request->image->extension();

        $request->image->move(public_path('image'), $filename);

        $product->update($data);

        return response()->json($product);
    }
}
