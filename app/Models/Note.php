<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'color',
        'is_pinned',
        'folder_id',
        'user_id'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Format hex color to 6-digit hex and append opacity if requested.
     *
     * @param string|null $color
     * @param string|null $opacity Two-character hex opacity (e.g. '20')
     * @return string
     */
    public static function formatHexColor(?string $color, ?string $opacity = null): string
    {
        if (!$color) {
            return '';
        }

        // Standardize: strip trailing 'ff' if it is a 9-char hex code (e.g. #ffe065ff)
        if (strlen($color) === 9 && str_ends_with(strtolower($color), 'ff')) {
            $color = substr($color, 0, 7);
        }

        if ($opacity) {
            if (strtolower($color) === '#ffffff') {
                return '';
            }
            return $color . $opacity;
        }

        return $color;
    }
}