<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\AttributeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeService
{
    protected $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function getAllAttributes()
    {
        return $this->attributeRepository->getAll();
    }

    public function storeAttribute(array $data)
    {
        try {
            DB::beginTransaction();

            // 1. إنشاء السمة
            $attribute = $this->attributeRepository->create(['name' => $data['name']]);

            // 2. إنشاء القيم المرتبطة بها
            foreach ($data['values'] as $value) {
                $attribute->values()->create(['value' => $value]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing attribute: ' . $e->getMessage());
            return false;
        }
    }

    public function updateAttribute($id, array $data)
    {
        try {
            DB::beginTransaction();

            $attribute = $this->attributeRepository->findById($id);
            
            // 1. تحديث السمة الأساسية
            $this->attributeRepository->update($attribute, ['name' => $data['name']]);

            // 2. أسهل طريقة للـ Update في الـ 1-to-Many المعقدة هي حذف القيم القديمة وإنشاء الجديدة
            $attribute->values()->delete();
            
            foreach ($data['values'] as $value) {
                $attribute->values()->create(['value' => $value]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating attribute: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteAttribute($id)
    {
        try {
            $attribute = $this->attributeRepository->findById($id);
            // سيتم حذف القيم تلقائياً إذا كنت ضايف onDelete('cascade') في المايجريشن
            $this->attributeRepository->delete($attribute);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting attribute: ' . $e->getMessage());
            return false;
        }
    }
}