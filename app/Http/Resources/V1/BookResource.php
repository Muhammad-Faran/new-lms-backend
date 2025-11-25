<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
        'id' => $this->id,
        'name' => $this->name,
        'max_allowed_amount' => $this->max_allowed_amount,
        'min_allowed_amount' => $this->min_allowed_amount,
        'status' => $this->status
    ];
}

}
