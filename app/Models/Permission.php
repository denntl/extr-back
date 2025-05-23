<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as BasePermission;

/**
 * Class Permission
 * @package App\Models
 * @property string $description
 */
class Permission extends BasePermission
{
    use HasFactory;
}
