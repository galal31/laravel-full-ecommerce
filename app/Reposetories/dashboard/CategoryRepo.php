<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Category;

class CategoryRepo
{

    public function index(){
        $categories = Category::get();
        
        return $categories;
    }

    // get parent categories
    public function getParentCategories(){
        $categories = Category::where('parent_id',null)->get();
        return $categories;
    }

    public function getCategory($id){
        $category = Category::findOrFail($id);
        return $category;
    }

    public function toggleStatus($id){
        $cat = $this->getCategory($id);
        $cat->status = !$cat->status;
        $cat->save();
        return $cat;
    }

    public function store($data){
        
        $cat = Category::create($data);
        return $cat;
    }

    public function update($id,$data){
        $cat = $this->getCategory($id);
        $cat->update($data);
        return $cat;
    }

    public function destroy($id){
        $cat = $this->getCategory($id);
        $cat->delete();
        return $cat;
    }

    
}
