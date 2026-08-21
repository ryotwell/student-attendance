<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingCase extends Model
{
    public const CATEGORY_OPTIONS = [
        'AKADEMIK' => [
            'label' => 'Akademik',
            'badgeClass' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        ],
        'PERILAKU' => [
            'label' => 'Perilaku',
            'badgeClass' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        ],
        'KEHADIRAN' => [
            'label' => 'Kehadiran',
            'badgeClass' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        ],
        'SOSIAL' => [
            'label' => 'Sosial',
            'badgeClass' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        ],
        'LAINNYA' => [
            'label' => 'Lainnya',
            'badgeClass' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        ],
    ];

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_OPTIONS[$this->category]['label'] ?? $this->category;
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return self::CATEGORY_OPTIONS[$this->category]['badgeClass'] ?? self::CATEGORY_OPTIONS['LAINNYA']['badgeClass'];
    }

}
