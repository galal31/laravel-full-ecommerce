<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Slider;

class SliderRepo
{
    public function getSliders()
    {
        return Slider::latest()->get();
    }

    public function getSlider($id)
    {
        return Slider::find($id);
    }

    public function createSlider(array $data)
    {
        return Slider::create($data);
    }

    public function deleteSlider(Slider $slider): bool
    {
        return $slider->delete();
    }
}