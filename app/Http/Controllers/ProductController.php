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

        // dd($data);

        $product = Product::create($data);
        if ($product) {
            return response()->json([
                'message' => 'Product Created Successfully!!',
                'product' => $product
            ]);
        }
        return response()->json([
            'message' => 'Something goes wrong while creating a product!!.'
        ], 422);
    }

    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $destinationpath = 'uploads/products/';

        $request->validate([
            'title' => 'required',
            // 'description' => 'required',
            // 'image' => 'nullable'
            'required_image' => 'required|mimes:doc,docx,pdf,txt,csv,jpeg,jpg,png,gif|max:2048'

        ]);
        $data = $request->except('_method', 'image', 'required_image');
        // dd($data);
        $imageFile = $request->file('image');
        $required_image = $request->file('required_image');

        // dd($imageFile);
        if ($imageFile) {
            $extension = strrchr($imageFile->getClientOriginalName(), '.');
            $new_file_name = "product_" . time();
            $image = $imageFile->move($destinationpath, $new_file_name . $extension);
            $data['image'] = isset($image) ? $new_file_name . $extension : null;
        }

        if ($required_image) {
            $extension = strrchr($required_image->getClientOriginalName(), '.');
            $new_file_name = "reqproduct_" . time();
            $image = $required_image->move($destinationpath, $new_file_name . $extension);
            $data['required_image'] = isset($image) ? $new_file_name . $extension : null;
        }
        // dd($data);

        $update_product = Product::where(['id' => $id])->update($data);

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
