<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AttributeRequest;
use App\Services\dashboard\AttributeService;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function index()
    {
        $attributes = $this->attributeService->getAllAttributes();
        return view('dashboard.attributes.index', compact('attributes'));
    }

    public function store(AttributeRequest $request)
    {
        
        $success = $this->attributeService->storeAttribute($request->validated());

        if ($success) {
            return response()->json(['success' => true, 'message' => __('messages.done')]);
        }

        return response()->json(['success' => false, 'message' => __('messages.error')], 500);
    }

    public function update(AttributeRequest $request, $id)
    {
        $success = $this->attributeService->updateAttribute($id, $request->validated());

        if ($success) {
            return response()->json(['success' => true, 'message' => __('messages.done')]);
        }

        return response()->json(['success' => false, 'message' => __('messages.error')], 500);
    }

    public function destroy($id)
    {
        $success = $this->attributeService->deleteAttribute($id);

        if ($success) {
            return response()->json(['success' => true, 'message' => __('messages.done')]);
        }

        return response()->json(['success' => false, 'message' => __('messages.error')], 500);
    }
}