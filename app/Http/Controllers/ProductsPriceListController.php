<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\brands;
use App\Models\categories;
use App\Models\products;
use Illuminate\Http\Request;

class ProductsPriceListController extends Controller
{
    public function index()
    {
        $vendors = accounts::vendor()->currentBranch()->get();
        $brands = brands::currentBranch()->get();
        $categories = categories::currentBranch()->get();

        return view('products.pricelist', compact('vendors', 'brands', 'categories'));
    }

    public function priceListData(Request $request)
    {

        $vendor = $request->vendor;
        $category = $request->category;
        $brand = $request->brand;

        $products = products::currentBranch()->with('vendor', 'units')
            ->when($category != 'all', function ($query) use ($category) {
                $query->where('catID', $category);
            })
            ->when($brand != 'all', function ($query) use ($brand) {
                $query->where('brandID', $brand);
            })
            ->when($vendor != 'all', function ($query) use ($vendor) {
                $query->where('vendorID', $vendor);
            })
            ->get();

        $vendor = $vendor != 'all' ? accounts::find($vendor)->title : 'All';
        $category = $category != 'all' ? categories::find($category)->name : 'All';
        $brand = $brand != 'all' ? brands::find($brand)->name : 'All';

        return view('products.pricelistdetails', compact('products', 'vendor', 'category', 'brand'));
    }
}
