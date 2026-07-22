<?php

namespace App\Reposetories\dashboard;

use App\Models\Page;

class PageRepo
{
    /**
     * Create a new class instance.
     */
    public function getPages(){
        return Page::query();
    }

    public function getPage ($id){
        return Page::find($id);
    }

    public function createPage(array $data){
        return Page::create($data);
    }

    public function updatePage($id,$data){
        return Page::find($id)->update($data);
    }

    public function deletePage($id){
        return Page::find($id)->delete();
    }
}
