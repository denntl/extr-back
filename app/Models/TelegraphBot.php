<?php

namespace App\Models;

use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithStringExternalId;
use Database\Factories\TelegraphBotFactory;
use DefStudio\Telegraph\Models\TelegraphBot as BaseTelegraphBot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Class TelegraphBot
 *
 * @property int $id
 * @property int $company_id
 * @property string $token
 * @property bool $is_active
 * @property string $name
 * @property string $public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegraphBot extends BaseTelegraphBot
{
    use WithExternalId;
    use WithStringExternalId;

    protected $fillable = [
        'token',
        'name',
        'company_id',
        'is_active',
        'public_id',
    ];

    /**
     * @return TelegraphBotFactory
     */
    protected static function newFactory(): Factory
    {
        return TelegraphBotFactory::new();
    }

    public function scopeCompany(Builder $builder)
    {
        $builder->join('companies', 'companies.id', '=', 'telegraph_bots.company_id');
    }
}
