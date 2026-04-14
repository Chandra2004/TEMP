<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

/**
 * @method static \TheFramework\App\QueryBuilder query()
 */
class RequirementParameter extends Model
{
    protected $table = 'requirement_parameters';
    protected $primaryKey = 'uid';

    protected $fillable = [
        'uid',
        'parameter_key',
        'display_name',
        'input_type',
        'input_options',
        'allowed_operators',
        'description',
        'is_active'
    ];

    /**
     * Get input options as array
     */
    public function getOptionsAttribute()
    {
        return json_decode($this->input_options ?? '[]', true);
    }

    /**
     * Get allowed operators as array
     */
    public function getOperatorsAttribute()
    {
        return json_decode($this->allowed_operators ?? '[]', true);
    }
}
