<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Image;

class ProductController extends Controller
{
    public function index()
    {
        $products  = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('product', compact('products'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:80',
            'image' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filenameWithoutEx = Str::slug($request->name) . '-' . time();
            $filename = $filenameWithoutEx . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/products', $filename);

            $img = Image::make(storage_path('app/public/products/' . $filename));  
            $img->text('DaengWeb.id', 200, 150, function($font) {  
                $font->file(public_path('milkyroad.ttf'));  
                $font->size(50);  
                $font->color('#e74c3c');
                $font->align('center');  
                $font->valign('middle');  
                $font->angle(30);  
            });
            $filenameWatermark = $filenameWithoutEx . '_watermark.' . $file->getClientOriginalExtension();
            $img->save(storage_path('app/public/products/' . $filenameWatermark));

            Product::create([
                'name' => $request->name,
                'original_image' => $filename,
                'image' => $filenameWatermark
            ]);
            return redirect()->back()->with(['success' => 'Produk Berhasil Di Unggah']);
        }
        return redirect()->back()->with(['error' => 'File Gambar Tidak Ditemukan']);
    }
}
