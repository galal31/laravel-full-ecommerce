<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\FaqRequest;
use App\services\dashboard\FaqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // استدعاء الـ Log مهم جداً

class FaqController extends Controller
{
    private $faqService;

    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    public function index()
    {

        if (request()->ajax()) {
            return $this->faqService->index();
        }
        return view('dashboard.faqs.index');
    }

    public function store(FaqRequest $request)
    {
        
        $data = $request->validated();
        
        try {
            $this->faqService->store($data);
            return response()->json([
                'success' => true,
                'message' => __('faqs.added_successfully')
            ]);
        } catch (\Throwable $th) {
            // تسجيل الخطأ الفعلي في السيرفر عشان ترجعله وقت الحاجة
            Log::error('FAQ Store Error: ' . $th->getMessage());

            // إرجاع رد JSON بـ Status 500 عشان الـ AJAX يقراه كخطأ
            return response()->json([
                'success' => false,
                'message' => __('messages.error')
            ], 500);
        }
    }

    public function update(FaqRequest $request, $id)
    {
        $data = $request->validated();
        try {
            $this->faqService->update($id, $data);
            return response()->json([
                'success' => true,
                'message' => __('faqs.updated_successfully')
            ]);
        } catch (\Throwable $th) {
            Log::error('FAQ Update Error (ID: ' . $id . '): ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.error')
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->faqService->destroy($id);
            return response()->json([
                'success' => true,
                'message' => __('faqs.deleted_successfully')
            ]);
        } catch (\Throwable $th) {
            Log::error('FAQ Delete Error (ID: ' . $id . '): ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.error')
            ], 500);
        }
    }
}