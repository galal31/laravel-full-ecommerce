<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\SliderRepo;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class SliderService
{
    use UploadFileTrait;
    public function __construct(
        protected SliderRepo $sliderRepository
    ) {}

    public function getSlidersForDataTable()
    {
        $sliders = $this->sliderRepository->getSliders();

        return DataTables::of($sliders)
            ->addIndexColumn()

            ->addColumn('image', function ($slider) {
                return '
                    <img src="' . $slider->file_name . '"
                         alt="slider"
                         style="width: 120px; height: 60px; object-fit: cover; border-radius: 6px;">
                ';
            })

            ->editColumn('note', function ($slider) {
                return $slider->getTranslation('note', app()->getLocale());
            })

            ->editColumn('created_at', function ($slider) {
                return $slider->created_at?->format('Y-m-d h:i A');
            })

            ->addColumn('actions', function ($slider) {
                return '
                    <button type="button"
                            class="btn btn-sm btn-danger delete-slider-btn"
                            data-delete-url="' . route('dashboard.sliders.destroy', $slider->id) . '">
                        <i class="ft-trash"></i> ' . __('messages.delete') . '
                    </button>
                ';
            })

            ->rawColumns(['image', 'actions'])
            ->make(true);
    }

    public function createSlider(array $data)
    {
        if (isset($data['file_name'])) {
            $image = $data['file_name'];

            $fileName = $this->uploadFile($image, 'sliders');
            $data['file_name'] = $fileName;
        }
        Cache::forget('sliders');

        return $this->sliderRepository->createSlider($data);
    }

    public function deleteSlider($id): bool
    {
        $slider = $this->sliderRepository->getSlider($id);

        if (! $slider) {
            return false;
        }

        if ($slider->getRawOriginal('file_name')) {
            $path = public_path('uploads/sliders/' . $slider->getRawOriginal('file_name'));

            if (file_exists($path)) {
                unlink($path);
            }
        }
        Cache::forget('sliders');

        return $this->sliderRepository->deleteSlider($slider);
    }
}