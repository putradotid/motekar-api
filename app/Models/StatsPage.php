<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatsPage extends Model
{
    protected $fillable = 
    [
        'label_1', 'value_1',
        'label_2', 'value_2',
        'label_3', 'value_3',
        'label_4', 'value_4',
    ];
}
