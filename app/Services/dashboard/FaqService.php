<?php

namespace App\services\dashboard;

use App\Http\Requests\dashboard\FaqRequest;
use App\reposetories\dashboard\FaqRepo;
use Yajra\DataTables\DataTables;

class FaqService
{
    /**
     * Create a new class instance.
     */
    protected $faqRepo;
    public function __construct(FaqRepo $faqRepo)
    {
        $this->faqRepo = $faqRepo;
    }

    public function index(){
        $faqs = $this->faqRepo->index();
        return DataTables::of($faqs)
        ->addColumn('actions',function($faq){
            return view('dashboard.faqs._actions',compact('faq'));
        })
        ->editColumn('question',function($faq){
            return $faq->getTranslation('question',app()->getLocale());
        })
        ->editColumn('answer',function($faq){
            return $faq->getTranslation('answer',app()->getLocale());
        })
        ->addIndexColumn()
        ->make(true);
    }

    public function store($data){
        $faq = $this->faqRepo->store($data);
        return $faq;
    }

    public function faqById($id){
        $faq = $this->faqRepo->faqById($id);
        return $faq;
    }

    public function update($id, $data){
        $faq = $this->faqRepo->update($id, $data);
        return $faq;
    }

    public function destroy($id){
        $faq = $this->faqRepo->destroy($id);
        return $faq;
    }
}
