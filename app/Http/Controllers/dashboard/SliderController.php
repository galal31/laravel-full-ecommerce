<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Services\dashboard\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __construct(
        protected SliderService $sliderService
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->sliderService->getSlidersForDataTable();
        }

        return view('dashboard.sliders.index');
    }

    public function store(SliderRequest $request)
    {
        $slider = $this->sliderService->createSlider($request->validated());

        if (! $slider) {
            return response()->json([
                'status' => 'error',
                'message' => __('sliders.error_occurred'),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('sliders.added_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->sliderService->deleteSlider($id);

        if (! $deleted) {
            return response()->json([
                'status' => 'error',
                'message' => __('sliders.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('sliders.deleted_successfully'),
        ]);
    }
}
