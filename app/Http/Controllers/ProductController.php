<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id','title','count','image')->get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'count'=>'required',
            'image'=>'required|image'
        ]);
        try{
            $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('product/image', $request->image,$imageName);
            Product::create($request->post()+['image'=>$imageName]);
            return response()->json([
                'message'=>'Продукт был создан успешно!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Что-то пошло не так!'
            ],500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'product'=>$product
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
      


       
        try{
            if (($request->count >= 100) && ($request->w == 0)){
                return response()->json([
                   'message'=>'Ограничение по количеству продукта: не более 100шт.!'
                 ]);   
            } 

            if (($request->count <= 0) && ($request->w != 0)) {
                return response()->json([
                    'message'=>'Продукты не могут быть меньше 0!'
                ]);
            }



            $product->fill($request->post())->update();
            if($request->hasFile('image')){
               if($product->image){
                    $exists = Storage::disk('public')->exists("product/image/{$product->image}");
                    if($exists){
                        Storage::disk('public')->delete("product/image/{$product->image}");
                    }
                }
                $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('product/image', $request->image,$imageName);
                $product->image = $imageName;
                $product->save();
            }

            if ($request->has('w')) {
                $action = $request->w;
                if ($action == 0) {
                    Product::countIncrement($request->id);    
                } else {
                    Product::countDecrement($request->id);    
                }
            }
        

        return response()->json([
            'message'=>'Продукт обновлен успешно!'
        ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Что-то пошло не так!'
            ],500);
        }
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            if($product->image){
                $exists = Storage::disk('public')->exists("product/image/{$product->image}");
                if($exists){
                    Storage::disk('public')->delete("product/image/{$product->image}");
                }
            }
            $product->delete();
            return response()->json([
                'message'=>'Продукт удален!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Что-то пошло не так!'
            ]);
        }
    }
}