<?php


namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{
    public function get()
    {
        $cart = ShoppingCart::where('user_id',Auth::id());

        if (! $cart->first())
        {
            return response()->json(['message' => 'basketIsEmpty']);
        }

        $products = ShoppingCart::where('user_id',Auth::id())->with('productDetail')->get()->toArray();
        $totalProductQuantityInBasket = $cart->sum('total_product_quantity');
        $totalProductPriceInBasket = $cart->sum('total_price');

        return response()->json([
            'data' => [
                'totalProductQuantityInBasket' => $totalProductQuantityInBasket,
                'totalProductPriceInBasket' => $totalProductPriceInBasket,
                'products' => $products
        ]]);
    }

    public function create(Request $request)
    {
        $issetCart = ShoppingCart::where('user_id',Auth::id())->get()->toArray();
        if ($issetCart)
        {
            return $this->update($request,intval($request->input('product_id')));
        }

        $validator = Validator::make($request->all(),[
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::find($productId);

        if (! $product)
        {
            return response()->json(['error' => 'notFoundProduct'],404);
        }

        $totalPrice = Product::where('id',$productId)->sum('price');
        $totalPrice = $totalPrice * $quantity;

        $cart = new ShoppingCart();
        $cart->user_id = Auth::id();
        $cart->product_id = $productId;
        $cart->total_price = $totalPrice;
        $cart->total_product_quantity = $quantity;
        $cart->save();

        return response()->json(['status' => 'success']);

    }

    public function update(Request $request,int $id)
    {
        $product = Product::find($id);
        if (!$product)
        {
            return response()->json(['error' => 'notFoundProduct','message' => 'invalidID'],404);
        }

        $validator = Validator::make($request->all(),[
            'quantity' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }


        $quantity = intval($request->input('quantity'));

        $cart = ShoppingCart::where('user_id',Auth::id())->where('product_id',$id)->first();

        if ($cart)
        {
            if ($request->input('quantity') <= 0)
            {
                $totalPrice = $product->price * abs($quantity); // negative to positive

                $cart->total_price = $cart->total_price - $totalPrice;
                $cart->total_product_quantity = $cart->total_product_quantity - abs($quantity);
                $cart->save();
            }else{
                $totalPrice = $product->price * $quantity;
                $cart->total_price = $cart->total_price + $totalPrice;
                $cart->total_product_quantity = $cart->total_product_quantity + $quantity;
                $cart->save();
            }
        }else{
            $totalPrice = $product->price * $quantity;

            $newProductAdd = new ShoppingCart();
            $newProductAdd->user_id = Auth::id();
            $newProductAdd->product_id = $id;
            $newProductAdd->total_price = $totalPrice;
            $newProductAdd->total_product_quantity = $quantity;
            $newProductAdd->save();

        }

        return response()->json(['status' => 'success']);

    }

    public function discharge($id = null)
    {


        if ($id != null)
        {
            $product = Product::find($id);
            if (!$product)
            {
                return response()->json(['error' => 'notFoundProduct','message' => 'invalidID'],404);
            }

            ShoppingCart::where('user_id',Auth::id())->where('product_id',$id)->delete();

            return response()->json(['status' => 'success']);
        }else{
            ShoppingCart::where('user_id',Auth::id())->delete();

            return response()->json(['status' => 'success']);
        }


    }
}
