<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     title="VersionCollection",
 *     description="Collection of versions list",
 *     type="object",
 *     @OA\Xml(
 *         name="VersionCollection"
 *     )
 * )
 */
class VersionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/VersionResource"))
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
