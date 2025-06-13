<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Board extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
    ];

    //Regra para gerar slog unicos automaticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($board) {
            if (empty($board->slug)) {
                $slug = $baseSlug = Str::slug($board->name);
                $counter = 1;

                // Verificar se o slug já existe
                while (DB::table('boards')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $board->slug = $slug;
            }
        });
    }

    public function columns() {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tasks() {
        return $this->hasManyThrough(Task::class, Column::class);
    }
}
