<?php

namespace TheFramework\Models;

use TheFramework\App\Model;

class CategoryRequirement extends Model
{
    protected $table = 'category_requirements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'uid_event_category',
        'parameter_name',
        'parameter_type',
        'parameter_value',
        'operator',
        'is_required',
        'priority',
        'error_message',
        'notes'
    ];

    protected $casts = [
        'parameter_value' => 'json',
        'is_required' => 'boolean'
    ];

    public function eventCategory()
    {
        return $this->belongsTo(EventCategory::class, 'uid_event_category', 'uid');
    }
}
