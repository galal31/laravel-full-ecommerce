<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait UploadFileTrait
{
    /**
     * ليه عملنا تريت منفصل؟ 
     * لأن رفع الصور عملية هتتكرر في (المنتجات، الأقسام، المستخدمين). 
     * كتابة الكود في كل Service هيعمل تكرار (Don't Repeat Yourself - DRY).
     */
    public function uploadFile($file, $disk, $oldFile = null)
    {
        // ليه بنشيك على الملف القديم هنا؟ 
        // عشان نوفر خطوة في الـ Service ونتأكد إن السيرفر مش هيتملي بصور ملهاش لازمة عند التحديث.
        if ($oldFile) {
            $this->deleteFile($oldFile, $disk);
        }

        // ليه بنستخدم store وما بنستخدمش move؟
        // دالة store بتولد اسم فريد (Hash) تلقائياً وتحمينا من تداخل أسماء الملفات، وبترجع المسار جاهز.
        return $file->store('/', $disk);
    }

    public function deleteFile($fileName, $disk)
    {
        // ليه بنشيك إن الملف موجود الأول (exists)؟
        // عشان لو مسار الملف متسجل في الداتا بيز بس الصورة ممسوحة من السيرفر لأي سبب، الكود ميضربش Error.
        if ($fileName && Storage::disk($disk)->exists($fileName)) {
            Storage::disk($disk)->delete($fileName);
        }
    }
}