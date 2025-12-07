<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboTaskItem extends Model
{
    protected $fillable = [
        'combo_task_id',
        'product_id',
        'sequence_order',
    ];

    public function comboTask()
    {
        return $this->belongsTo(ComboTask::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}