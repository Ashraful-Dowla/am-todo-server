<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Step;

class AssignedTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'assigned_to'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
