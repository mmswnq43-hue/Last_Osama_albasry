<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    protected $fillable = [
        'title', 'description', 'image_path',
        'link_url', 'is_active', 'sort_order',
        'start_date', 'end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    /** الإعلانات النشطة المعروضة حالياً */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) =>
                $q->whereNull('start_date')->orWhere('start_date', '<=', now())
            )
            ->where(fn ($q) =>
                $q->whereNull('end_date')->orWhere('end_date', '>=', now())
            )
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /** رابط الصورة (S3 presigned أو local) */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) return null;

        $disk = config('filesystems.default', 'public');
        try {
            return $disk === 's3'
                ? Storage::disk('s3')->temporaryUrl($this->image_path, now()->addHour())
                : Storage::disk($disk)->url($this->image_path);
        } catch (\Throwable) {
            return null;
        }
    }
}
