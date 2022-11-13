<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends ApiFormRequest
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
            'categoryName' => 'required|string',
        ];

        switch ($method) {
            case 'POST':
                $this->data= $rules;
                break;
            case 'PUT':
                $this->data= [
                        'categoryId' => 'required|integer|exists:categories,id',
                    ] + $rules;
                break;
            case 'DELETE':
                $this->data= [
                    'categoryId' => 'required|integer|exists:categories,id'
                ];
                break;
        }
        return $this->data;
    }
    protected function prepareForValidation()
    {
        $cutId=($this->categoryId)?['id' => $this->categoryId]:[];
        $this->merge($cutId+[
            'name' => $this->categoryName,//myshop.local/models/CategoriesModel.php -> line => 73
            'parent_id' => $this->categoryParentId??0, //myshop.local/models/CategoriesModel.php -> line => 76
        ]);
    }
}
