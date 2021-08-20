<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('client.credentials')->only(['index']);
        $this->middleware('auth:api')->except(['index']);

        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['index', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {
        //sync, attach, syncWithoutDetaching
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse('La categoria especificado no es una categoria de este producto', 404);
        }

        $product->categories()->detach([$category->id]);

        return $this->showAll($product->categories);
    }
}
