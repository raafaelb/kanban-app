<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'column_id',
        'board_id',
        'order'
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
