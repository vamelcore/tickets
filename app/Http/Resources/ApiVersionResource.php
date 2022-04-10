<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiVersionResource extends JsonResource
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
            'version' => $this->version,
            'status' => $this->status,
            'deprecated_date' => $this->when(null != $this->deprecated_date, function () {
                return $this->deprecated_date->format('m.d.Y');
            }),
            'example_url' => $this->when('current' == $this->status, function () {
                return env('APP_URL').'api/'.$this->version;
            })
        ];
    }
}
