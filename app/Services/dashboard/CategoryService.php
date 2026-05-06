<?php

namespace App\Services\dashboard;

use App\Models\Dashboard\Category;
use App\Reposetories\dashboard\CategoryRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    /**
     * Create a new class instance.
     */
    protected $categoryRepo;
    public function __construct(CategoryRepo $categoryRepo)
    {
        //
        $this->categoryRepo = $categoryRepo;
    }

    public function index()
    {
        
        $categories = $this->categoryRepo->index();
        return DataTables::of($categories)
            ->addColumn('name', function ($category) {
                return $category->getTranslation('name', app()->getLocale());
            })
            ->editColumn('status', function ($category) {
                return $category->status == 1 ? __('categories.active') : __('categories.inactive');
            })
            ->editColumn('icon', function ($category) {
                return '<img src="' . $category->icon . '" alt="' . $category->getTranslation('name', app()->getLocale()) . '" width="50" height="50">';
            })
            ->addIndexColumn()
            ->addColumn('actions', function ($category) {
                return view('dashboard.categories._actions', compact('category'));
            })
            ->addColumn('parent', function ($category) {
                return $category->parent ? $category->parent->getTranslation('name', app()->getLocale()) : __('categories.no_parent');
            })
            ->rawColumns(['actions', 'icon'])
            ->make(true);
    }

    // get parent categories
    public function getParentCategories()
    {
        return $this->categoryRepo->getParentCategories();
    }

    public function getCategory($id)
    {
        return $this->categoryRepo->getCategory($id);
    }
    public function toggleStatus($id)
    {
        try {
            $this->categoryRepo->toggleStatus($id);
            return true;

        } catch (\Throwable $th) {
            return false;
        }
    }


    public function store($data)
    {
        $cat = $this->categoryRepo->store($data);
        return $cat;
    }

    public function update($id, $data)
    {
        $cat = $this->categoryRepo->update($id, $data);
        return $cat;
    }

    public function destroy($id)
    {
        try {
            return $this->categoryRepo->destroy($id);
        } catch (\Throwable $th) {
            Log::error('category delete error'.$id.' '.$th->getMessage());
            return false;
        }
    }
}
