<?php

namespace App\Http\Controllers;

use App\Filters\CategoryFilters;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter= new CategoryFilters();
        $queryItems=$filter->transform($request);//[['column','operator','value']]
        $categories= Category::where($queryItems);
        $includeParentId=$request->query('includeCategoryParentIds');
        if($includeParentId){
            //myshop.local/models/CategoriesModel.php -> line => 42
            $categories=$categories->with('categoryparentids');
        }
        $includeProduct=$request->query('includeCategoryProducts');
        if($includeProduct){
            $categories=$categories->with('categoryproducts');
        }
        //myshop.local/models/CategoriesModel.php -> line => 62
        return new CategoryCollection($categories->orderBy('parent_id', 'asc')->paginate(10)->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return CategoryResource|\Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        //myshop.local/models/CategoriesModel.php -> line => 51
        return new CategoryResource(Category::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //myshop.local/models/CategoriesModel.php -> line => 32,15,6
        if($category) {
            $includeParentIds = request()->query('includeCategoryParentIds');
            if ($includeParentIds) {
                $category = $category->loadMissing('categoryparentids');
            }
            $includeProduct = request()->query('includeCategoryProducts');
            if ($includeProduct) {
                $category = $category->loadMissing('categoryproducts');
            }
            return response()->json(data: [
                'status'=>true,
                'message'=>"Category showed.",
                'data'=>new CategoryResource($category)
            ]);
        }else{
            return response()->json([
                'message'=>"Category ID not found",
                'status'=>false
            ]);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //myshop.local/models/CategoriesModel.php -> line => 80
        $category->update($request->all());
        return response()->json([
            'message'=>"Category Updated.",
            'status'=>true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        return response()->json([
            'id'=>$category->id,
            'status'=>$category->delete(),
            'message'=>'Category Deleted.'
        ]);
    }
}
