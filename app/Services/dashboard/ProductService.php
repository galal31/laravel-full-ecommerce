<?php

namespace App\Services\dashboard;

use App\Models\Dashboard\Product;
use App\Reposetories\dashboard\ProductRepository;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadFileTrait;
use Exception;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductService
{
    use UploadFileTrait;

    protected $productRepo;

    // حقن الـ Repository هنا
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function storeProduct(array $data, array $images = [])
    {
        DB::beginTransaction();

        try {
            // 1. تجهيز التاجز (Tags)
            $tagIds = [];
            if (!empty($data['tags'])) {
                // تمشي علي التاجزات المدخلة وتطبق عليها trim
                $tags = array_map('trim', explode(',', $data['tags']));
                foreach ($tags as $tag) {
                    $tagModel = $this->productRepo->firstOrCreateTag($tag);
                    $tagIds[] = $tagModel->id;
                }
            }

            // 2. تجهيز بيانات المنتج الأساسية (مع تطبيق اللوجيك اللي اتكلمنا عليه)
            $productData = [
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'sku' => $data['sku'],
                'available_for' => $data['available_for'] ?? null,
                'price' => $data['has_variants'] ? 0 : $data['price'],
                'quantity' => (!$data['has_variants'] && $data['manage_stock']) ? $data['quantity'] : null,
                'manage_stock' => $data['has_variants'] ? 0 : $data['manage_stock'],
                'discount' => $data['has_discount'] ? $data['discount'] : null,
                'start_discount' => $data['has_discount'] ? $data['start_discount'] : null,
                'end_discount' => $data['has_discount'] ? $data['end_discount'] : null,
                'name' => $data['name'],
                'small_desc' => $data['small_desc'],
                'desc' => $data['desc'],
            ];

            // 3. إنشاء المنتج
            $product = $this->productRepo->createProduct($productData);

            // 4. ربط التاجز
            if (!empty($tagIds)) {
                $this->productRepo->syncTags($product, $tagIds);
            }

            // 5. إنشاء المتغيرات (Variants) وخصائصها
            if ($data['has_variants']) {
                foreach ($data['prices'] as $index => $price) {
                    $variant = $this->productRepo->createVariant($product, [
                        'price' => $price,
                        'stock' => $data['quantities'][$index] ?? 0,
                    ]);

                    if (isset($data['attribute_values'][$index])) {
                        $valuesToAttach = array_filter($data['attribute_values'][$index]);
                        if (!empty($valuesToAttach)) {
                            $this->productRepo->attachVariantAttributes($variant, $valuesToAttach);
                        }
                    }

                    // ====== السطور الجديدة لمعالجة صور المتغير ======
                    if (!empty($data['variantImages'][$index])) {
                        // سنستخدم مجلد 'products' أو 'variants' حسب إعداداتك في config/filesystems.php
                        $this->uploadFiles($data['variantImages'][$index], 'variants', $variant); 
                    }
                    // ===============================================
                }
            }

            // 6. رفع الصور
            if (!empty($images)) {
                $this->uploadFiles($images, 'products', $product);
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            // بنرمي الـ Exception عشان الـ Livewire يمسكه ويطلع رسالة الخطأ للمستخدم
            throw $e;
        }
    }

    // datatables
    public function getTableData()
    {
        $products = $this->productRepo->getAllProductsQuery();

        return DataTables::of($products)
            ->addIndexColumn()
            // عرض الاسم بناءً على اللغة الحالية (بافتراض أن الحقل JSON)
            ->editColumn('name', fn($row) => $row->getTranslation('name', app()->getLocale()))

            // عرض القسم
            ->addColumn('category', fn($row) => $row->category ? $row->category->getTranslation('name', app()->getLocale()) : '---')

            // تبسيط منطق السعر: إذا وجد متغيرات نبه المستخدم، وإلا اعرض السعر الأساسي
            ->editColumn('price', function ($row) {
                return $row->variants->isNotEmpty()
                    ? '<span class="badge badge-info">له متغيرات</span>'
                    : $row->price . ' ج.م';
            })
            ->editColumn('brand', function ($row) {
                return $row->brand ? $row->brand->getTranslation('name', app()->getLocale()) : '---';
            })

            // الحالة (Status)
            ->editColumn('status', function ($row) {
                $class = $row->status ? 'success' : 'danger';
                $text = $row->status ? __('datatables.active') : __('datatables.inactive');
                return "<span class='badge badge-$class'>$text</span>";
            })

            // الأزرار
            ->addColumn('actions', function ($row) {
                return view('dashboard.products.actions', compact('row'));
            })
            ->rawColumns(['price', 'status', 'actions'])
            ->make(true);
    }



    public function toggleStatus(string $id)
    {
        return $this->productRepo->toggleStatus($id);
    }


    public function getShowDetails($id)
    {
        return $this->productRepo->getProductWithDetails($id);
    }
    public function getProductForEdit($id)
    {
        return $this->productRepo->getProductForEdit($id);
    }

    public function updateProduct(Product $product, array $data, array $newProductImages = [])
    {
        DB::beginTransaction();
        try {
            // 1. تحديث البيانات الأساسية
            $dataToUpdate = [
                'name' => ['ar' => $data['name_ar'], 'en' => $data['name_en']],
                'small_desc' => ['ar' => $data['small_desc_ar'], 'en' => $data['small_desc_en']],
                'desc' => ['ar' => $data['desc_ar'], 'en' => $data['desc_en']],
                'category_id'        => $data['category_id'],
                'brand_id'           => $data['brand_id'],
                'sku'                => $data['sku'],
                'price'              => $data['has_variants'] ? 0 : $data['price'],
                'quantity'           => (!$data['has_variants'] && $data['manage_stock']) ? ($data['quantity'] ?? 0) : null,
                'has_variants'       => $data['has_variants'],
                'manage_stock'       => $data['has_variants'] ? 0 : $data['manage_stock'],
                'has_discount'       => $data['has_discount'],
                'discount'           => $data['has_discount'] ? $data['discount_percentage'] : null,
                'start_discount'     => $data['has_discount'] ? $data['start_discount'] : null,
                'end_discount'       => $data['has_discount'] ? $data['end_discount'] : null,
                'available_for'      => $data['available_for'],
            ];

            $this->productRepo->update($product, $dataToUpdate);

            // 2. حذف صور المنتج الأساسية (التي طلب المستخدم حذفها)
            if (!empty($data['deleted_product_images'])) {
                foreach ($data['deleted_product_images'] as $imgId) {
                    $image = $this->productRepo->getProductImageById($imgId);
                    if ($image) {
                        $this->deleteFile($image->file_name, 'products');
                        $this->productRepo->deleteProductImage($image);
                    }
                }
            }

            // 3. رفع صور المنتج الجديدة
            if (!empty($newProductImages)) {
                $this->uploadFiles($newProductImages, 'products', $product);
            }

            // 4. حذف المتغيرات (التي طلب المستخدم حذفها)
            if (!empty($data['deleted_variants'])) {
                foreach ($data['deleted_variants'] as $vId) {
                    $variant = $this->productRepo->getVariantById($vId);
                    if ($variant) {
                        // مسح صور المتغير من السيرفر قبل حذف المتغير نفسه
                        foreach ($variant->images as $img) {
                            $this->deleteFile($img->file_name, 'products');
                        }
                        $this->productRepo->deleteVariant($variant);
                    }
                }
            }

            // 5. حذف صور المتغيرات المعينة
            if (!empty($data['deleted_variant_images'])) {
                foreach ($data['deleted_variant_images'] as $imgId) {
                    $image = $this->productRepo->getVariantImageById($imgId);
                    if ($image) {
                        $this->deleteFile($image->file_name, 'products');
                        $this->productRepo->deleteVariantImage($image);
                    }
                }
            }

            // 6. التعامل مع المتغيرات (تحديث أو إضافة)
            if ($data['has_variants'] && !empty($data['variantRows'])) {
                foreach ($data['variantRows'] as $index) {
                    $vData = [
                        'price' => $data['prices'][$index] ?? 0,
                        'stock' => $data['quantities'][$index] ?? 0,
                    ];

                    // التحقق مما إذا كان المتغير موجود مسبقاً (للتحديث) أم جديد (للإضافة)
                    if (isset($data['variant_ids'][$index]) && !empty($data['variant_ids'][$index])) {
                        $variant = $this->productRepo->getVariantById($data['variant_ids'][$index]);
                        $variant->update($vData);
                    } else {
                        $variant = $this->productRepo->createVariant($product, $vData);
                    }

                    // مزامنة السمات (Attributes)
                    if (isset($data['attribute_values'][$index])) {
                        $valuesToAttach = array_filter($data['attribute_values'][$index]);
                        if (!empty($valuesToAttach)) {
                            // نستخدم sync بدلاً من attach لكي يحذف القديم ويضيف الجديد
                            $variant->attributeValues()->sync($valuesToAttach);
                        }
                    }

                    // رفع صور جديدة لهذا المتغير
                    if (!empty($data['newVariantImages'][$index])) {
                        $this->uploadFiles($data['newVariantImages'][$index], 'products', $variant);
                    }
                }
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }
}
