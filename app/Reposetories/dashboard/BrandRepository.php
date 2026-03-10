<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Brand;
use Illuminate\Database\Eloquent\Builder;

class BrandRepository
{
    /**
     * ليه بنرجع Builder (استعلام لم يُنفذ) وليه مش بنرجع Collection (داتا فعلية) باستخدام get()؟
     * لأن الـ DataTables هتاخد الـ Builder وتضيف عليه شروط البحث (Search) والتقسيم (Pagination).
     * لو استخدمنا get() هنجيب كل البراندات في الميموري (Memory Leak لو الداتا كبيرة)، وده خطأ فادح في الأداء.
     */
    public function cloneBaseQuery(): Builder
    {
        return Brand::query()->latest();
    }

    public function findById($id): Brand
    {
        // ليه findOrFail مش find؟
        // عشان لو الـ ID مش موجود، لارافيل ترمي ModelNotFoundException وتتحول تلقائياً لـ 404، بدل ما يضرب Error في الكود تحت.
        return Brand::findOrFail($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): bool
    {
        // ليه بنمرر الـ Model كامل مش الـ ID؟
        // عشان في الـ Service بنكون عملنا عليه find بالفعل، فمش منطقي نعمل استعلام تاني في الداتا بيز جوه دالة الـ update.
        return $brand->update($data);
    }

    public function delete(Brand $brand): bool|null
    {
        return $brand->delete();
    }
}