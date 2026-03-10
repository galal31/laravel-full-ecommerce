<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\CreateCategoryRequest;
use App\Models\Dashboard\Category;
use App\Services\dashboard\CategoryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $categoriesService;
    public function __construct(CategoryService $categoriesService)
    {
        $this->categoriesService = $categoriesService;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->categoriesService->index();
        }
        $parents = $this->categoriesService->getParentCategories();
        return view('dashboard.categories.index', compact('parents'));
    }


    /**
     * Show the form for creating a new resource.
     */

    public function store(CreateCategoryRequest $request){
        
        $data = $request->validated();
        $cat = $this->categoriesService->store($data);
        return response()->json(['success' => true, 'message' =>__('messages.done')]);
    }

    public function update($id, CreateCategoryRequest $request){
        $data = $request->validated();
        $cat = $this->categoriesService->update($id,$data);
    }

    public function destroy($id){
        $cat = $this->categoriesService->destroy($id);
        if(!$cat){
            return response()->json(['success' => false, 'message' =>__('messages.error')]);
        }
        return response()->json(['success' => true, 'message' =>__('messages.done')]);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function toggleStatus($id)
    {
       try {
        $this->categoriesService->toggleStatus($id);
        return response()->json(['success' => true, 'message' =>__('messages.status_updated_successfully')]);
       } catch (\Throwable $th) {
        return response()->json(['success' => false, 'message' =>__('messages.error')]);
       }
    }

    
}
