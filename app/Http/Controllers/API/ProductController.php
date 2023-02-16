<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class ProductController extends Controller
{

    public $successStatus = 200;
    public $invalidStatus = 201;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $product = Product::where("delete_flag", false)->find($id);
        if (!empty($product)) {
            $product->user;
            $product->category;
        }
        return response()->json(['status' => "success", 'data' => $product], $this->successStatus);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['category_id'] = (int) $input['category_id'];
        $input['user_id'] = Auth::user()->id;
        $input["delete_flag"] = false;

        DB::beginTransaction();

        try {
            $product = Product::create($input);
            
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
        }

        return response()->json(['status'=> "success", 'data' => [$product]], $this->successStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();

        $product = Product::find($input['id']);
                        
        if (!$product->delete_flag && $product->user_id == Auth::user()->id) {
            DB::beginTransaction();

            try {
                $product["name"] = $input["name"];
                $product["category_id"] = $input["category_id"];
                $product->save();
                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
            }
            
            return response()->json(['status'=> "success", 'data' => $product ], $this->successStatus);
        } else {
            if ($product->delete_flag)
                $message =  "Category was already deleted.";
            if ($product->user_id != Auth::user()->id)
                $message =  "You can't update this category.";
        }
        
        return response()->json(['status'=> "fail", 'message' => $message ], $this->successStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct($id)
    {
        $product = Product::find($id);
                        
        if (!$product->delete_flag && $product->user_id == Auth::user()->id) {
            DB::beginTransaction();

            try {
                $product["delete_flag"] = true;
                $product->save();
                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
            }
            
            return response()->json(['status'=> "success", 'data' => $product ], $this->successStatus);
        } else {
            if ($product->delete_flag)
                $message =  "Category was already deleted.";
            if ($product->user_id != Auth::user()->id)
                $message =  "You can't delete this category.";
        }
        
        return response()->json(['status'=> "fail", 'message' => $message ], $this->successStatus);
        // return response()->json(['success'=>$success], $this->successStatus);
    }
}
