<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bomVersions(): HasMany
    {
        return $this->hasMany(BomVersion::class);
    }

    public function latestVersionNumber(): int
    {
        return $this->bomVersions()->max('version');
    }

    public function latestBomVersion(): BomVersion
    {
        return $this->bomVersions()->latest('version')->first();
    }
}
