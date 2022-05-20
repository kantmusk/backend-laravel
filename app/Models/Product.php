<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'count', 'image'];

       

    public function countIncrement($id = 1)

    {

    	static::where('id',$id)->increment('count',1);

    }

    
    public function countDecrement($id = 1)

    {

    	static::where('id',$id)->decrement('count',1);

    }

}
