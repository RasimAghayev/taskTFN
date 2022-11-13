<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'productId'=>$this->id,
            'productCategoryId'=>$this->category_id,
            'productName'=>$this->name,
            'productDescription'=>$this->description,
            'productPrice'=>$this->price,
            'productImage'=>$this->image,
            'productStatus'=>$this->status,
            'productCreatedAt' => $this->created_at->format('d.m.Y H:i:s'),
            'productUpdatedAt' => $this->updated_at->format('d.m.Y H:i:s'),
//            'orders'=> OrderResource::collection($this->whenLoaded('categoryparentids')),
//            'purchase'=>PurchaseResource::collection($this->whenLoaded('categoryproducts')),
        ];
    }
}
