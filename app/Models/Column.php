<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Column extends Model
{
    protected $fillable = ['title', 'color', 'board_id', 'order'];

    public function tasks() {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    public function board() {
        return $this->belongsTo(Board::class);
    }
}
