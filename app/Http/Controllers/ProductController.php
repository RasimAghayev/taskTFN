<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilters;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter= new ProductFilters();
        $queryItems=$filter->transform($request);//[['column','operator','value']]
        $products= Product::where($queryItems);
        $includeParentId=$request->query('includeProductOrders');
        if($includeParentId){
            //myshop.local/models/CategoriesModel.php -> line => 42
            $products=$products->with('productorders');
        }
        $includeProduct=$request->query('includeProductPurchases');
        if($includeProduct){
            $products=$products->with('productpurchase');
        }
        //myshop.local/models/CategoriesModel.php -> line => 62
        return new ProductCollection($products->orderBy('id', 'desc')->paginate(10)->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return ProductResource|\Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        //myshop.local/models/CategoriesModel.php -> line => 51
        return new ProductResource(Product::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //myshop.local/models/CategoriesModel.php -> line => 32,15,6
        if($product) {
            $includeParentIds = request()->query('includeProductOrders');
            if ($includeParentIds) {
                $product = $product->loadMissing('productorders');
            }
            $includeProduct = request()->query('includeProductPurchases');
            if ($includeProduct) {
                $product = $product->loadMissing('productpurchase');
            }
            return response()->json(data: [
                'status'=>true,
                'message'=>"Product showed.",
                'data'=>new ProductResource($product)
            ]);
        }else{
            return response()->json([
                'message'=>"Product ID not found",
                'status'=>false
            ]);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //myshop.local/models/CategoriesModel.php -> line => 80
        $product->update($request->all());
        return response()->json([
            'message'=>"Product Updated.",
            'status'=>true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        return response()->json([
            'id'=>$product->id,
            'status'=>$product->delete(),
            'message'=>'Product Deleted.'
        ]);
    }
}
