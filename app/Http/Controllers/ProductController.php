<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected Konsumen $konsumen;
    public function __construct(
        Category $category,
    )
    {
        $this->perPage = config('sid.default.table.perpage');
        // $this->perPage = 100;
        $this->category = $category;
    }
    public function index(Request $request)
    {
        $grouped = Category::with('manyProd')->get()->map(function($cat) {
            return [
                'category' => $cat->cat_name,
                'products' => $cat->manyProd->map(function($p) {
                    return [
                        'name' => $p->prod_name,
                        'desc' => $p->prod_desc,
                        'image' => $p->image_path,
                    ];
                })->toArray()
            ];
        });

        return view('products.index', ['produkList' => $grouped]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $json = json_decode($request->input('products'), true);

        if (!$json || !isset($json['category']) || !isset($json['name']) || count($json['name']) === 0) {
            return redirect()->back()->withErrors(['products' => 'Format produk tidak valid!']);
        }

        // Simpan kategori dulu
        $today = Carbon::now()->format('Ymd');
        $lastCat = Category::whereDate('created_at', Carbon::today())->latest()->first();
        $seqCat = $lastCat ? ((int) Str::afterLast($lastCat->cat_code, '-') + 1) : 1;
        $catCode = 'CAT-' . $today . '-' . str_pad($seqCat, 4, '0', STR_PAD_LEFT);

        $category = Category::create([
            'cat_code' => $catCode,
            'cat_name' => $json['category'],
        ]);

        // Loop setiap produk
        foreach ($json['name'] as $index => $prodData) {
            $prodName = $prodData['name'] ?? null;
            $prodDesc = $prodData['desc'] ?? 'Deskripsi belum diisi';

            if (!$prodName) continue;

            // Handle gambar
            $imagePath = null;
            $imageKey = "images.$index";
            if ($request->hasFile($imageKey)) {
                $imageFile = $request->file($imageKey);
                $imagePath = $imageFile->store('uploads/products', 'public');
            }

            // Generate kode produk
            $lastProduct = Product::whereDate('created_at', Carbon::today())->latest()->first();
            $sequence = $lastProduct ? ((int) Str::afterLast($lastProduct->prod_code, '-') + 1) : 1;
            $prodCode = 'PRD-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Simpan produk
            Product::create([
                'prod_code'     => $prodCode,
                'prod_name'     => $prodName,
                'prod_desc'     => $prodDesc,
                'categories_id' => $category->id,
                'image_path'    => $imagePath,
            ]);
        }

        return redirect()->route('products')->with('success', 'Produk berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
