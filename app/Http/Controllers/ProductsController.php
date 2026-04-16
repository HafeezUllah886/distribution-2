<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\branches;
use App\Models\brands;
use App\Models\categories;
use App\Models\product_units;
use App\Models\products;
use App\Models\units;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($s_cat, $s_brand)
    {
        $products = products::currentBranch();

        if ($s_cat != 'all') {
            $products->where('catID', $s_cat);
        }

        if ($s_brand != 'all') {
            $products->where('brandID', $s_brand);
        }

        $items = $products->get();

        $cats = categories::orderBy('name', 'asc')->currentBranch()->get();
        $brands = brands::orderBy('name', 'asc')->currentBranch()->get();

        return view('products.product', compact('items', 'cats', 'brands', 's_cat', 's_brand'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cats = categories::orderBy('name', 'asc')->currentBranch()->get();
        $brands = brands::orderBy('name', 'asc')->currentBranch()->get();
        $units = units::currentBranch()->get();
        $vendors = accounts::vendor()->currentBranch()->get();

        return view('products.create', compact('cats', 'brands', 'units', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'unique:products,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'name.unique' => 'Product already Existing',
            ]
        );

        $product = products::create($request->only(['name', 'nameurdu', 'catID', 'brandID', 'pprice', 'price', 'discount', 'status', 'vendorID', 'fright', 'labor', 'claim', 'sfright', 'sclaim', 'discountp']) + ['branchID' => auth()->user()->branchID]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $product->id.'.'.$image->getClientOriginalExtension();
            $destPath = public_path('images/products');
            if (! file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            $image->move($destPath, $filename);

            $manager = new ImageManager(new Driver);
            $manager->read($destPath.'/'.$filename)
                ->cover(300, 200)
                ->save($destPath.'/'.$filename);

            $product->image_path = 'images/products/'.$filename;
            $product->save();
        }

        $units = $request->unit_names;

        foreach ($units as $key => $unit) {
            product_units::create(
                [
                    'productID' => $product->id,
                    'unit_name' => $unit,
                    'value' => $request->unit_values[$key],
                ]
            );
        }

        return back()->with('success', 'Product Created');
    }

    /**
     * Display the specified resource.
     */
    public function show($all)
    {
        $categories = categories::with('products')->get();

        return view('products.pricelist', compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $product)
    {
        $cats = categories::orderBy('name', 'asc')->currentBranch()->get();
        $brands = brands::orderBy('name', 'asc')->currentBranch()->get();
        $vendors = accounts::vendor()->currentBranch()->get();
        $branches = branches::orderBy('name', 'asc')->get();

        return view('products.edit', compact('cats', 'brands', 'product', 'vendors', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'unique:products,name,'.$id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'name.unique' => 'Product already Existing',
            ]
        );

        $product = products::find($id);
        $product->update($request->only(['name', 'nameurdu', 'catID', 'brandID', 'pprice', 'price', 'discount', 'status', 'vendorID', 'fright', 'labor', 'claim', 'sfright', 'sclaim', 'discountp', 'branchID']));

        if ($request->hasFile('image')) {
            if ($product->image_path && file_exists(public_path($product->image_path))) {
                unlink(public_path($product->image_path));
            }

            $image = $request->file('image');
            $filename = $product->id.'.'.$image->getClientOriginalExtension();
            $destPath = public_path('images/products');
            if (! file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            $image->move($destPath, $filename);

            $manager = new ImageManager(new Driver);
            $manager->read($destPath.'/'.$filename)
                ->cover(300, 200)
                ->save($destPath.'/'.$filename);

            $product->image_path = 'images/products/'.$filename;
            $product->save();
        }

        return redirect()->back()->with('success', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(products $products)
    {
        //
    }
}
