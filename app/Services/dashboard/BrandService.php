<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\BrandRepository;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandService
{
    use UploadFileTrait;

    protected $brandRepo;

    // ليه بنحقن الـ Repository هنا (Dependency Injection)؟
    // عشان لو قررنا نغير طريقة جلب الداتا مستقبلاً، هنغير في الـ Repo بس من غير ما نلمس الـ Service.
    public function __construct(BrandRepository $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }

    public function getBrandsQuery()
    {
        return $this->brandRepo->cloneBaseQuery();
    }

    public function storeBrand($request)
    {
        // ليه استخدمنا DB::transaction؟
        // لو رفعنا الصورة بنجاح بس الداتا بيز ضربت Error، الصورة هتفضل متعلقة في السيرفر بدون داتا. 
        // الـ transaction بيضمن إن يا إما كل حاجة تتم (رفع صورة + حفظ داتا)، يا إما يلغي كل حاجة لو حصل خطأ.
        return DB::transaction(function () use ($request) {
            
            // استبعاد الداتا اللي ملهاش عمود في الداتا بيز
            $data = $request->except(['_token', 'logo']);
            
            // توليد الـ slug تلقائياً لو المستخدم مدخلهوش
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']['en'] ?? reset($data['name']));
            }

            // رفع الصورة إن وجدت
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadFile($request->file('logo'), 'brands');
            }

            return $this->brandRepo->create($data);
        });
    }

    public function updateBrand($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $brand = $this->brandRepo->findById($id);
            $data = $request->except(['_token', '_method', 'logo']);

            if ($request->hasFile('logo')) {
                // ليه بنستخدم getRawOriginal؟
                // لو عامل Accessor في الموديل بيضيف الـ URL للصورة، getRawOriginal بتجيب الاسم الأصلي بس عشان نعرف نمسحه من السيرفر.
                $data['logo'] = $this->uploadFile($request->file('logo'), 'brands', $brand->getRawOriginal('logo'));
            }

            $this->brandRepo->update($brand, $data);
            return $brand;
        });
    }

    public function deleteBrand($id)
    {
        return DB::transaction(function () use ($id) {
            $brand = $this->brandRepo->findById($id);
            
            if ($brand->logo) {
                $this->deleteFile($brand->getRawOriginal('logo'), 'brands');
            }

            return $this->brandRepo->delete($brand);
        });
    }

    public function toggleStatus($id)
    {
        $brand = $this->brandRepo->findById($id);
        // dd($brand->getRawOriginal('status'));
        $current_status = $brand->getRawOriginal('status');
        return $this->brandRepo->update($brand, ['status' => !$current_status]);
    }
}