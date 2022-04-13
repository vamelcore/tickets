<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="VersionResource",
 *     description="API Version item",
 *     @OA\Xml(
 *         name="VersionResource"
 *     )
 * )
 */
class VersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     * @OA\Property(format="string", title="version", default="v1", description="version name", property="version"),
     * @OA\Property(format="string", title="status", default="active", description="Can be: current / active / deprecated", property="status"),
     * @OA\Property(format="string", title="deprecated_date", default="10.10.2022", description="date when API will be deprecated", property="deprecated_date"),
     * @OA\Property(format="string", title="example_url", default="http(s)://localhost/api/v1/", description="example API url", property="example_url")
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
