<?php

namespace App\reposetories\dashboard;

use App\Models\Dashboard\Faq;

class FaqRepo
{
    /**
     * Create a new class instance.
     */
    public function index(){
        $faqs = Faq::get();
        return $faqs;
    }

    public function store($data){
        $faq = Faq::create($data);
        return $faq;
    }

    public function faqById($id){
        $faq = Faq::find($id);
        return $faq;
    }

    public function update($id, $data){
        $faq = $this->faqById($id);
        $faq->update($data);
        return $faq;
    }

    public function destroy($id){
        $faq = $this->faqById($id);
        $faq->delete();
        return $faq;
    }
}
