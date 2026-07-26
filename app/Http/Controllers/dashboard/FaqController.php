<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\FaqRequest;
use App\Services\dashboard\FaqService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(private FaqService $faqService) {}

    public function index(): View
    {
        $faqs = $this->faqService->getAll();

        return view('dashboard.faqs.index', compact('faqs'));
    }

    public function store(FaqRequest $request): JsonResponse
    {
        try {
            $this->faqService->store($request->validated());

            return response()->json([
                'success' => true,
                'message' => __('faqs.added_successfully'),
            ]);
        } catch (\Throwable $th) {
            Log::error('FAQ Store Error', ['exception' => $th]);

            return response()->json([
                'success' => false,
                'message' => __('faqs.error_occurred'),
            ], 500);
        }
    }

    public function update(FaqRequest $request, int $id): JsonResponse
    {
        try {
            $this->faqService->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => __('faqs.updated_successfully'),
            ]);
        } catch (ModelNotFoundException $th) {
            throw $th;
        } catch (\Throwable $th) {
            Log::error('FAQ Update Error', ['faq_id' => $id, 'exception' => $th]);

            return response()->json([
                'success' => false,
                'message' => __('faqs.error_occurred'),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->faqService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => __('faqs.deleted_successfully'),
            ]);
        } catch (ModelNotFoundException $th) {
            throw $th;
        } catch (\Throwable $th) {
            Log::error('FAQ Delete Error', ['faq_id' => $id, 'exception' => $th]);

            return response()->json([
                'success' => false,
                'message' => __('faqs.error_occurred'),
            ], 500);
        }
    }
}
