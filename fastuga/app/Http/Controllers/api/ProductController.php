<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreUpdateProductRequest;
use App\Http\Requests\UpdateCompleteProductRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(Product::orderByRaw("FIELD(type, \"cold dish\", \"hot dish\", \"drink\", \"dessert\")")->get());
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(StoreUpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->update($validated);

        if (array_key_exists('photo', $validated)) {
            Storage::delete('storage/products/' . $product->photo_url);
            $image_64 = $validated['photo'];
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

            if ($extension == 'jpeg')
                $extension = 'jpg';

            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;

            Storage::put('public/products/' . $imageName, base64_decode($image));

            $product->photo_url = $imageName;
            $product->save();
        }

        return new ProductResource($product);
    }

    public function store(StoreUpdateProductRequest $request)
    {
        $validated = $request->validated();

        $image_64 = $validated['photo'];
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

        if ($extension == 'jpeg')
            $extension = 'jpg';

        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::random(16) . '.' . $extension;

        Storage::put('public/products/' . $imageName, base64_decode($image));

        $newProduct = Product::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'photo_url' => $imageName,
        ]);

        return new ProductResource($newProduct);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return new ProductResource($product);
    }
    /*
    public function update_completed(UpdateCompleteProductRequest $request, Product $product)
    {
        $product->completed = $request->validated()['completed'];
        $product->save();
        return new ProductResource($product);
    }*/
}
