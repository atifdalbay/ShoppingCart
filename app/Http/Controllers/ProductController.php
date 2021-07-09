<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function get()
    {
        $products = Product::all();
        return response()->json(['data' => $products]);
    }

    public function getById($id)
    {
        $product = Product::find($id);
        if (! $product)
        {
            return response()->json(['error' => 'notFoundProduct'],404);
        }

        $product = Product::where('id',$id)->get()->toArray();
        return response()->json(['data' => $product]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->save();

        return response()->json(['status' => 'success','created_id' => $product->id],200);
    }

    public function update(Request $request,$id)
    {
        $product = Product::find($id);
        if (! $product)
        {
            return response()->json(['error' => 'notFoundProduct'],404);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->save();

        return response()->json(['status' => 'success','updated_id' => $product->id],200);
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if (! $product)
        {
            return response()->json(['error' => 'notFoundProduct'],404);
        }

        Product::destroy($id);

        return response()->json(['status' => 'success', 'deleted_id' => $id]);
    }
}
