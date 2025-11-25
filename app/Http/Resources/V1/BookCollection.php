<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($book) {
            return [
                'id' => $book->id,
                'name' => $book->name,
                'max_allowed_amount' => $book->max_allowed_amount,
                'min_allowed_amount' => $book->min_allowed_amount,
                'status' => $book->status
            ];
        });
    }
}
