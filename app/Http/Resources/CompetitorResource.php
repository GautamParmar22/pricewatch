<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // GP - 19-08-2026 code comment - Transform competitor resource
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'website_url' => $this->websiteUrl ?? $this->website_url,
            'pricing_url' => $this->pricingUrl ?? $this->pricing_url,
            'status' => $this->status,
            'check_frequency' => $this->check_frequency,
            'last_checked_at' => $this->last_checked_at?->toIso8601String(),
            'next_check_at' => $this->next_check_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String()
        ];
    }
}
