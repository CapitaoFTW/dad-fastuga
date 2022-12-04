<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreUpdateProductRequest;
use App\Http\Requests\UpdateCompleteProductRequest;

class ProductController extends Controller
{
    /*public function getProductsOfUser(Request $request, User $user)
    {
        //ProductResource::$format = 'detailed';
        if (!$request->has('include_assigned')) {
            return ProductResource::collection($user->products->sortByDesc('id'));
        } else {
            return ProductResource::collection($user->products->merge($user->assigedProducts)->sortByDesc('id'));
        }
    }*/

    public function index() {

        return ProductResource::collection(Product::get());
    }

    /*public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function store(StoreUpdateProductRequest $request)
    {
        $newProduct = Product::create($request->validated());
        return new ProductResource($newProduct);
    }

    public function update(StoreUpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->assignedUsers()->detach();
        $product->delete();
        return new ProductResource($product);
    }

    public function update_completed(UpdateCompleteProductRequest $request, Product $product)
    {
        $product->completed = $request->validated()['completed'];
        $product->save();
        return new ProductResource($product);
    }*/
}
