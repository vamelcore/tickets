<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'token' => $this->when(null != $this->token, function(){
                return $this->token;
            }),
            'created_at' => $this->created_at->format(config('app.date_format_full')),
            'updated_at' => $this->updated_at->format(config('app.date_format_full'))
        ];
    }
}
