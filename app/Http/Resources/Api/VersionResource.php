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
     * @OA\Property(type="string", example="v1", description="Version preffix", property="version"),
     * @OA\Property(type="string", example="active", description="Status of API version", property="status", enum={"current", "active", "deprecated"}),
     * @OA\Property(type="string", format="date", example="10.10.2022", description="Date when API will be deprecated", property="deprecated_date"),
     * @OA\Property(type="string", example="http(s)://localhost/api/v1/", description="Example API url", property="example_url")
     */
    public function toArray($request)
    {
        return [
            'version' => $this->version,
            'status' => $this->status,
            'deprecated_date' => $this->when(null != $this->deprecated_date, function () {
                return $this->deprecated_date->format(config('app.date_format_short'));
            }),
            'example_url' => $this->when('current' == $this->status, function () {
                return env('APP_URL').'api/'.$this->version;
            })
        ];
    }
}
