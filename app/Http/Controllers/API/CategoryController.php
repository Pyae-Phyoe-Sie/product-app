<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class CategoryController extends Controller
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
        $category = Category::where("delete_flag", false)find($id);
        if (!empty($category)) {
            $category->user;
            $category->products;
        }
        return response()->json(['success' => $category], $this->successStatus);
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
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input["delete_flag"] = false;

        DB::beginTransaction();

        try {
            $category = Category::create($input);
            
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
        }

        // return response()->json(['success'=>$success], $this->successStatus);
        return response()->json(['status'=> "success", 'data' => [$category]], $this->successStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();

        $category = Category::find($input['id']);
                        
        if (!$category->delete_flag && $category->user_id == Auth::user()->id) {
            DB::beginTransaction();

            try {
                $category["name"] = $input["name"];
                $category->save();
                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
            }
            
            return response()->json(['status'=> "success", 'data' => [$category]], $this->successStatus);
        } else {
            if ($category->delete_flag)
                $message =  "Category was already deleted.";
            if ($category->user_id != Auth::user()->id)
                $message =  "You can't update this category.";
        }
        
        return response()->json(['status'=> "fail", 'message' => [$message]], $this->successStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // 
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function delete(Category $category)
    {
        //
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function deleteCategory($id)
    {
        $category = Category::find($id);
                        
        if (!$category->delete_flag && $category->user_id == Auth::user()->id) {
            DB::beginTransaction();

            try {
                $category["delete_flag"] = true;
                $category->save();
                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                return response()->json(['status' => "fail", 'message' => $e], $this->invalidStatus);
            }
            return response()->json(['status'=> "success", 'data' => $category ], $this->successStatus);
        } else {
            if ($category->delete_flag)
                $message =  "Category was already deleted.";
            if ($category->user_id != Auth::user()->id)
                $message =  "You can't delete this category.";
        }
        
        // return response()->json(['success'=>$success], $this->successStatus);
        return response()->json(['status'=> "fail", 'message' => $message ], $this->successStatus);
    }
}
