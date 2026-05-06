<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Category;
use App\Traits\UploadFileTrait;

class CategoryRepo
{
    use UploadFileTrait;

    public function index()
    {
        $categories = Category::get();

        return $categories;
    }

    // get parent categories
    public function getParentCategories()
    {
        $categories = Category::where('parent_id', null)->get();
        return $categories;
    }

    public function getCategory($id)
    {
        $category = Category::findOrFail($id);
        return $category;
    }

    public function toggleStatus($id)
    {
        $cat = $this->getCategory($id);
        $cat->status = !$cat->status;
        $cat->save();
        return $cat;
    }

    public function store($data)
    {
        if (isset($data['icon'])) {
            $data['icon'] = $this->uploadFile($data['icon'], 'categories');
        }

        $cat = Category::create($data);
        return $cat;
    }

    public function update($id, $data)
    {
        $cat = $this->getCategory($id);
        if (isset($data['icon'])) {
            $data['icon'] = $this->uploadFile($data['icon'], 'categories', $cat->getRawOriginal('icon'));
        }
        $cat->update($data);
        return $cat;
    }

    public function destroy($id)
    {
        $cat = $this->getCategory($id);
        $cat->delete();
        return $cat;
    }


}
