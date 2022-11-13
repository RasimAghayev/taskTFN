<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends ApiFormRequest
{
    private $data=[];
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $method=$this->method();
        $rules = [
            'productCategoryId' => 'required|integer',
            'productName' => 'required|string',
            'productDescription' => 'required|string',
            'productPrice' => 'required|numeric',
            'productImage' => 'required|image|file',
            'productStatus' => 'required|in:0,1',
        ];

        switch ($method) {
            case 'POST':
                $this->data= $rules;
                break;
            case 'PUT':
                $this->data= [
                        'productId' => 'required|integer|exists:products,id',
                    ] + $rules;
                break;
            case 'DELETE':
                $this->data= [
                    'productId' => 'required|integer|exists:products,id'
                ];
                break;
        }
        return $this->data;
    }
    protected function prepareForValidation()
    {
        $proId=($this->productId)?['id' => $this->productId]:[];
        $this->merge($proId+[
            'category_id' => $this->productCategoryId,//myshop.local/models/CategoriesModel.php -> line => 73
            'name' => $this->productName,//myshop.local/models/CategoriesModel.php -> line => 73
            'description' => $this->productDescription,//myshop.local/models/CategoriesModel.php -> line => 73
            'price' => $this->productPrice, //myshop.local/models/CategoriesModel.php -> line => 76
            'image' => $this->productImage, //myshop.local/models/CategoriesModel.php -> line => 76
            'status' => $this->productStatus??1, //myshop.local/models/CategoriesModel.php -> line => 76
        ]);
    }
}
