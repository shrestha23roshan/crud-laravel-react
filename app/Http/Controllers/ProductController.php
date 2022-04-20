<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $destinationpath = 'uploads/products/';

        $request->validate([
            'title' => 'required',
            'required_image' => 'required|mimes:doc,docx,pdf,txt,csv,jpeg,jpg,png,gif|max:2048'
        ]);

        $data = $request->except('image', 'required_image');
        $imageFile = $request->file('image');
        $required_image = $request->file('required_image');

        if ($imageFile) {
            $extension = strrchr($imageFile->getClientOriginalName(), '.');
            $new_file_name = "product_" . time();
            $image = $imageFile->move($destinationpath, $new_file_name . $extension);
            $data['image'] = isset($image) ? $new_file_name . $extension : NULL;
        }

        if ($required_image) {
            $extension = strrchr($required_image->getClientOriginalName(), '.');
            $new_file_name = "reqproduct_" . time();
            $image = $required_image->move($destinationpath, $new_file_name . $extension);
            $data['required_image'] = isset($image) ? $new_file_name . $extension : NULL;
        }

        $product = Product::create($data);
        if ($product) {
            return response()->json([
                'message' => 'Product Created Successfully!!',
                'product' => $product
            ]);
        }
        return response()->json([
            'message' => 'Something goes wrong while creating a product!!.'
        ], 500);
    }

    public function show(Product $product)
    {
        // return response()->json([
        //     'product' => $product
        // ]);
        return $product;
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $destinationpath = 'uploads/products/';

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'image' => 'nullable'
        ]);
        $product = $request->except('image');
        dd($product);
        $imageFile = $request->image;
        // dd($imageFile);
        if ($imageFile) {
            $extension = strrchr($imageFile->getClientOriginalName(), '.');
            $new_file_name = "product_" . time();
            $image = $imageFile->move($destinationpath, $new_file_name . $extension);
            $data['image'] = isset($image) ? $new_file_name . $extension : null;
        }

        $update_product = Product::where(['id' => $id])->update($product);

        if ($update_product) {
            return response()->json([
                'message' => 'Product updated!',
                'product_update' => $update_product
            ]);
        }
        return response()->json([
            'message' => 'Update Error',
        ], 500);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        if ($product) {
            return response()->json([
                'message' => "Product delete succesfully."
            ]);
        }
        return response()->json([
            'message' => "Problem in Delete Product."
        ], 500);
    }
}
