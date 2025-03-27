<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable=['name','amount','user_id','group_id'];

    public function user(){
        return $this->belongsTo(User::class); 
    }


    public function group()
    {
        return $this->belongsTo(Group::class); 
    }


    public function tags(){
        return $this->belongsToMany(Tag::class, 'expense_tag');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'expense_participants')
            ->withPivot('amount_paid', 'split_type');
    }
    
}
