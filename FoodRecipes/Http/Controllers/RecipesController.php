<?php

namespace App\Http\Controllers;

use App\Recipes;
use Illuminate\Http\Request;

class RecipesController extends Controller
{
    /**
     * Load a single recipe
     * @param string $recipe
     */
    public function view($recipe = '')
    {
        return $this->cacheForOneDay(view('frontend.pages.recipe'));
    }

}
