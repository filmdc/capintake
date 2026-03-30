<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NpiGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_number',
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'goal_number' => 'integer',
        ];
    }

    // --- Relationships ---

    public function indicators(): HasMany
    {
        return $this->hasMany(NpiIndicator::class);
    }
}
