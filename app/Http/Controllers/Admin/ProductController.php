<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productType']);

        // Filtros
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('country')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.country_of_origin')) = ?", [$request->country]);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'low_stock') {
                $query->where('stock_quantity', '<', 10);
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'productTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'productTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'image' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Procesar datos específicos del tipo de producto
        $productSpecificData = [];
        
        // Lista de campos específicos que pueden venir del formulario
        $specificFields = [
            'country_of_origin',
            'volume_ml', 
            'packaging_type',
            'alcohol_content',
            'beer_style',
            'brewery',
            'ibu',
            'srm',
            'ingredients',
            'tasting_notes'
        ];
        
        // Procesar cada campo específico
        foreach ($specificFields as $fieldName) {
            if ($request->has($fieldName) && $request->$fieldName !== null && $request->$fieldName !== '') {
                $productSpecificData[$fieldName] = $request->$fieldName;
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id,
            'product_type_id' => $request->product_type_id,
            'product_specific_data' => $productSpecificData,
            'image' => $request->image,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'productType', 'orderItems.order');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'productTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'image' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Procesar datos específicos del tipo de producto
        $productSpecificData = $product->product_specific_data ?? []; // Preservar datos existentes
        
        // Lista de campos específicos que pueden venir del formulario
        $specificFields = [
            'country_of_origin',
            'volume_ml', 
            'packaging_type',
            'alcohol_content',
            'beer_style',
            'brewery',
            'ibu',
            'srm',
            'ingredients',
            'tasting_notes'
        ];
        
        // Procesar cada campo específico
        foreach ($specificFields as $fieldName) {
            if ($request->has($fieldName)) {
                $value = $request->$fieldName;
                
                // Si el campo está vacío, lo eliminamos del array
                if ($value === null || $value === '') {
                    unset($productSpecificData[$fieldName]);
                } else {
                    $productSpecificData[$fieldName] = $value;
                }
            }
        }


        // Actualizar campos básicos
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id,
            'product_type_id' => $request->product_type_id,
            'image' => $request->image,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
        ]);

        // Actualizar product_specific_data por separado
        $product->product_specific_data = $productSpecificData;
        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
