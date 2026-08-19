<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceChangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // GP - 19-08-2026 code comment - Transform price change resource
        return [
            'id' => $this->id,
            'competitor' => $this->competitor->name,
            'plan' => $this->plan?->name ?? '—',
            'change_type' => $this->change_type,
            'field' => $this->field,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'percentage_change' => $this->percentage_change,
            'severity' => $this->severity,
            'detected_at' => $this->detected_at->toIso8601String()
        ];
    }
}
