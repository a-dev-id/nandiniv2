<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

class Role extends Model
{
    public const ADMINISTRATOR = 'administrator';

    public const SALES_MARKETING = 'sales-marketing';

    public const FINANCE = 'finance';

    public const AFFILIATE = 'affiliate';

    protected $fillable = ['name', 'slug'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function users(): MorphedByMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }

    public function affiliates(): MorphedByMany
    {
        return $this->morphedByMany(Affiliate::class, 'model', 'model_has_roles');
    }
}
