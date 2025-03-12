<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tag extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function expenses(){
        return $this->belongsToMany(Expense::class, 'expense_tag');
    }
}
