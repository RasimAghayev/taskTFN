<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'categoryId'=>$this->id,
            'categoryName'=>$this->name,
            'categoryParentId'=>$this->parent_id,
            'categoryCreatedAt' => $this->created_at->format('d.m.Y H:i:s'),
            'categoryUpdatedAt' => $this->updated_at->format('d.m.Y H:i:s'),
            'categoryParentIds'=> self::collection($this->whenLoaded('categoryparentids')),
            'products'=>ProductResource::collection($this->whenLoaded('categoryproducts')),
        ];
    }
}
