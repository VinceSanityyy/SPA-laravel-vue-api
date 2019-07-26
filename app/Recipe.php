<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use app\RecipeIngredient;
use app\RecipeDirection;
use app\User;

class Recipe extends Model
{
    //

    protected $fillable = [
        'name', 'description', 'image',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ingredients(){
        return $this->hasMany(RecipeIngredient::class);
    }

    public function directions(){
        return $this->hasMany(RecipeDirections::class);
    }

    public static function form(){
        return [
            'name' => '',
            'description' => '',
            'image' => '',
            'ingredients'=> [
                RecipeIngredient::form()
            ],
            'directions'=>[
                RecipeDirection::form(),
                RecipeDirection::form()
            ]
        ];
    }
}
