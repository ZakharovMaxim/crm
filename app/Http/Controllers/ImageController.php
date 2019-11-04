<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreImagesToProductRequest;
use App\Http\Requests\ChangeImageOrderRequest;
use App\Product;

class ImageController extends Controller
{
    public function store(StoreImagesToProductRequest $request)
    {
        $product = Product::find($request->input('parent_id'));
        if (isset($request->photos) && count($request->photos) && $product) {
            $files = [];
            $index = $request->input('last_order');
            $image_ids = [];
            foreach($request->photos as $photo)
            {
                $path = '/storage/'.basename($photo->store('public'));
                $files[] = $path;
                $img = Image::create([
                    'url' => $path
                ]);
                $image_ids[$img->id] = ['order' => $index];
                $index++;
            }
            $product->images()->attach($image_ids);
            return $product->images()->get();
        }
        return abort(404);
    }
    public function destroy(Image $image)
    {
        if ($image->url) {
            Storage::delete("public/" . basename($image->url));
        }
        $image->delete();
        return 'ok';
    }
    public function sort(ChangeImageOrderRequest $request)
    {
        $product = Product::find($request->input('parent_id'));
        $ids = [];
        foreach($request->input('list') as $item)
        {
            $ids[$item['id']] = ['order' => $item['order']];
        }
        $product->images()->sync($ids);
        return 'ok';
    }
}
