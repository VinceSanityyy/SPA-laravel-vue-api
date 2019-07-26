<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\RecipeIngredient;
use App\RecipeDirection;

class RecipeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')
            ->except('index', 'show');
    }

    public function index()
    {
        $recipes = Recipe::orderBy('created_at', 'desc')
            ->get(['name', 'image', 'id']);

        return response()
            ->json([
                'recipes' => $recipes
            ]);
    }

    public function create()
    {
        $form = Recipe::form();
        return response()
            ->json(['form' => $form]);
    }

    public function store()
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:4000',
            'image' => 'required|image',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|max:255',
            'ingredients.*.qty' => 'required|max:255',
            'directions' => 'required|array|min:1',
            'directions.*.description' => 'required|max:3000'
        ]);

        $ingredients = [];

        foreach($request->ingredients as $ingredient){
            $ingredients[] = new RecipeIngredient($ingredient);
        }
        $directions = [];

        foreach($request->directions as $direction){
            $directions[] = new RecipeDirection($direction);
        }

        if(!$request->hasFile('image')&& !$request->file('image')->isValid()){
            return abort(404, 'Image not uploaded');
        }
        $filename = $this->getFileName($request->image);
        $request->image->move(base_path('public/images'), $filename);

        $recipe = new Recipe($request->all());

        $recipe->image = $filename;
        $request->user()
            ->recipes()->save($recipe);

        $recipe->directions()
            ->saveMany($directions);

        $recipe->ingredients()
            ->saveMany($ingredients);

        return response()
            ->json([
                'save'=>true,
                'id' => $recipe->id,
                'message' => 'Successfully created'
            ]);
    }
    protected function getFileName($file)
    {
        return str_random(32).'.'.$file->extension();
    }

    public function show($id)
    {

    }
}
