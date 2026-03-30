<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'funding_source',
        'fiscal_year_start',
        'fiscal_year_end',
        'requires_income_eligibility',
        'fpl_threshold_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year_start' => 'date',
            'fiscal_year_end' => 'date',
            'requires_income_eligibility' => 'boolean',
            'fpl_threshold_percent' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // --- Relationships ---

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function serviceRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            ServiceRecord::class,
            Service::class,
        );
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // --- Helpers ---

    public function isClientEligible(Client $client): bool
    {
        if (! $this->requires_income_eligibility) {
            return true;
        }

        $fplPercent = $client->fplPercent();

        if ($fplPercent === null) {
            return false; // can't determine without FPL data
        }

        return $fplPercent <= $this->fpl_threshold_percent;
    }
}
