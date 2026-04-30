<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Services\Dashboard\ProductService;
use App\Models\Dashboard\{Category, Brand, Attribute};
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public int $productId; 
    public int $currentStep = 1;

    // الأساسيات
    public $name_ar = '';
    public $name_en = '';
    public $small_desc_ar = '';
    public $small_desc_en = '';
    public $desc_ar = '';
    public $desc_en = '';
    public $category_id = '';
    public $brand_id = '';
    public $available_for = '';
    
    public $sku = '';
    public $price = 0;
    public $quantity = 0;
    public $has_variants = 0;
    public $manage_stock = 0;
    public $has_discount = 0;
    public $discount_percentage = null;
    public $start_discount = '';
    public $end_discount = '';

    // بيانات المتغيرات (مفهرسة حسب ترتيب الصفوف)
    public $variantRows = []; 
    public $variant_ids = [];
    public $prices = [];
    public $quantities = [];
    public $attribute_values = [];
    
    // إدارة الصور (الحالية والجديدة والمحذوفة)
    public $existingProductImages = [];
    public $newProductImages = [];
    public $deleted_product_images = [];

    public $existingVariantImages = [];
    public $newVariantImages = [];
    public $deleted_variant_images = [];
    public $deleted_variants = [];

    public function mount(ProductService $productService, $productId)
    {
        $this->productId = $productId;
        $product = $productService->getProductForEdit($productId);

        $this->name_ar = $product->getTranslation('name', 'ar');
        $this->name_en = $product->getTranslation('name', 'en');
        $this->small_desc_ar = $product->getTranslation('small_desc', 'ar');
        $this->small_desc_en = $product->getTranslation('small_desc', 'en');
        $this->desc_ar = $product->getTranslation('desc', 'ar');
        $this->desc_en = $product->getTranslation('desc', 'en');
        
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;
        $this->available_for = $product->available_for;
        
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->quantity = $product->quantity;
        
        $this->has_variants = $product->variants->isNotEmpty() ? 1 : 0;
        $this->manage_stock = (bool) $product->manage_stock;
        
        $this->has_discount = $product->discount !== null ? 1 : 0;
        $this->discount_percentage = $product->discount;
        $this->start_discount = $product->start_discount;
        $this->end_discount = $product->end_discount;

        // تعبئة المتغيرات الموجودة
        if ($this->has_variants) {
            foreach ($product->variants as $index => $variant) {
                $this->variantRows[] = $index;
                $this->variant_ids[$index] = $variant->id;
                $this->prices[$index] = $variant->price;
                $this->quantities[$index] = $variant->stock;

                foreach ($variant->attributeValues as $attrVal) {
                    $this->attribute_values[$index][$attrVal->attribute_id] = $attrVal->id;
                }

                foreach ($variant->images as $img) {
                    $this->existingVariantImages[$index][$img->id] = $img->file_name;
                }
            }
        } else {
            $this->variantRows = [0]; // صف افتراضي فارغ لو قرر يضيف
        }

        // تعبئة صور المنتج الأساسية
        foreach ($product->images as $img) {
            $this->existingProductImages[$img->id] = $img->file_name;
        }
    }

    #[Computed]
    public function categories() { return Category::select('id', 'name')->where('status', 1)->get(); }

    #[Computed]
    public function brands() { return Brand::select('id', 'name')->where('status', 1)->get(); }

    #[Computed]
    public function productAttributes() { return Attribute::with('values')->get(); }

    public function rules()
    {
        return [
            // قواعد الخطوة الأولى: البيانات الأساسية
            1 => [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'small_desc_ar' => 'nullable|string|max:500',
                'small_desc_en' => 'nullable|string|max:500',
                'desc_ar' => 'nullable|string',
                'desc_en' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'available_for' => 'nullable|date',
            ],
            // قواعد الخطوة الثانية: التسعير والمخزون والمتغيرات
            2 => [
                'sku' => 'required|string|unique:products,sku,' . $this->productId,
                'has_variants' => 'required|boolean',
                'price' => $this->has_variants ? 'nullable' : 'required|numeric|min:0',
                'manage_stock' => 'boolean',
                'quantity' => (!$this->has_variants && $this->manage_stock) ? 'required|integer|min:0' : 'nullable',
                
                // التحقق من المتغيرات في حال تفعيلها
                'prices.*' => $this->has_variants ? 'required|numeric|min:0' : 'nullable',
                'quantities.*' => $this->has_variants ? 'required|integer|min:0' : 'nullable',
                'attribute_values.*.*' => $this->has_variants ? 'required|exists:attribute_values,id' : 'nullable',

                // التحقق من الخصومات في حال تفعيلها
                'has_discount' => 'boolean',
                'discount_percentage' => $this->has_discount ? 'required|integer|min:1|max:99' : 'nullable',
                'start_discount' => $this->has_discount ? 'required|date' : 'nullable',
                'end_discount' => $this->has_discount ? 'required|date|after_or_equal:start_discount' : 'nullable',
            ],
            // قواعد الخطوة الثالثة: الصور
            3 => [
                'newProductImages.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'newVariantImages.*.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]
        ];
    }
    // ====== وظائف الخطوات ======
    public function nextStep()
    {
        // جلب قواعد التحقق الخاصة بالخطوة الحالية
        $stepRules = $this->rules()[$this->currentStep] ?? [];
        
        if (!empty($stepRules)) {
            $this->validate($stepRules);
        }

        $this->currentStep++;
    }

    public function previousStep() { $this->currentStep--; }

    // ====== وظائف المتغيرات ======
    public function addVariant()
    {
        $newIndex = empty($this->variantRows) ? 0 : max($this->variantRows) + 1;
        $this->variantRows[] = $newIndex;
        $this->prices[$newIndex] = 0;
        $this->quantities[$newIndex] = 0;
    }

    public function removeVariant($index)
    {
        if (isset($this->variant_ids[$index])) {
            $this->deleted_variants[] = $this->variant_ids[$index];
        }
        
        $this->variantRows = array_diff($this->variantRows, [$index]);
        unset($this->variant_ids[$index], $this->prices[$index], $this->quantities[$index], $this->attribute_values[$index], $this->existingVariantImages[$index], $this->newVariantImages[$index]);
    }

    // ====== وظائف الصور ======
    public function removeExistingProductImage($imageId)
    {
        $this->deleted_product_images[] = $imageId;
        unset($this->existingProductImages[$imageId]);
    }

    public function removeNewProductImage($index)
    {
        unset($this->newProductImages[$index]);
    }

    public function removeExistingVariantImage($variantIndex, $imageId)
    {
        $this->deleted_variant_images[] = $imageId;
        unset($this->existingVariantImages[$variantIndex][$imageId]);
    }

    public function removeNewVariantImage($variantIndex, $imageIndex)
    {
        unset($this->newVariantImages[$variantIndex][$imageIndex]);
    }

    // ====== الحفظ ======
    public function save(ProductService $productService)
    {
        // دمج جميع قواعد التحقق لكل الخطوات للتحقق النهائي
        $allRules = array_merge($this->rules()[1], $this->rules()[2], $this->rules()[3]);
        $this->validate($allRules);

        // تجميع البيانات
        $data = [
            'name_ar' => $this->name_ar, 'name_en' => $this->name_en,
            'small_desc_ar' => $this->small_desc_ar, 'small_desc_en' => $this->small_desc_en,
            'desc_ar' => $this->desc_ar, 'desc_en' => $this->desc_en,
            'category_id' => $this->category_id, 'brand_id' => $this->brand_id, 'available_for' => $this->available_for,
            'sku' => $this->sku, 'price' => $this->price, 'quantity' => $this->quantity,
            'has_variants' => $this->has_variants, 'manage_stock' => $this->manage_stock,
            'has_discount' => $this->has_discount, 'discount_percentage' => $this->discount_percentage,
            'start_discount' => $this->start_discount, 'end_discount' => $this->end_discount,
            
            // بيانات المتغيرات والحذف
            'variantRows' => $this->variantRows,
            'variant_ids' => $this->variant_ids,
            'prices' => $this->prices,
            'quantities' => $this->quantities,
            'attribute_values' => $this->attribute_values,
            'deleted_variants' => $this->deleted_variants,
            'deleted_product_images' => $this->deleted_product_images,
            'deleted_variant_images' => $this->deleted_variant_images,
            'newVariantImages' => $this->newVariantImages,
        ];

        $product = $productService->getProductForEdit($this->productId);
        $productService->updateProduct($product, $data, $this->newProductImages);

        session()->flash('success', __('products.product_updated_successfully'));
        return redirect()->route('dashboard.products.index');
    }
};
?>

{{-- ==================== بداية ملف الـ Blade (HTML) ==================== --}}
<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">{{ __('products.edit_product') }}</h4>
            <div>
                <span class="badge {{ $currentStep == 1 ? 'badge-primary' : 'badge-secondary' }} p-2">1. {{ __('products.basic_info') }}</span>
                <span class="badge {{ $currentStep == 2 ? 'badge-primary' : 'badge-secondary' }} p-2">2. {{ __('products.pricing_inventory') }}</span>
                <span class="badge {{ $currentStep == 3 ? 'badge-primary' : 'badge-secondary' }} p-2">3. {{ __('products.product_images') }}</span>
            </div>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save">
                
                {{-- ---------------- الخطوة 1 ---------------- --}}
                @if($currentStep == 1)
                    <h5 class="mb-3 text-info"><i class="ft-info"></i> {{ __('products.basic_info') }}</h5>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>{{ __('products.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name_ar" class="form-control">
                            @error('name_ar') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('products.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name_en" class="form-control">
                            @error('name_en') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label>{{ __('products.small_desc_ar') }}</label>
                            <textarea wire:model="small_desc_ar" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('products.small_desc_en') }}</label>
                            <textarea wire:model="small_desc_en" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>{{ __('products.category') }} <span class="text-danger">*</span></label>
                            <select wire:model="category_id" class="form-control">
                                <option value="">{{ __('products.select_category') }}</option>
                                @foreach($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('products.brand') }}</label>
                            <select wire:model="brand_id" class="form-control">
                                <option value="">{{ __('products.select_brand') }}</option>
                                @foreach($this->brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        <button type="button" wire:click="nextStep" class="btn btn-info px-4">{{ __('products.next') }} <i class="ft-chevron-left"></i></button>
                    </div>
                @endif


                {{-- ---------------- الخطوة 2 ---------------- --}}
                @if($currentStep == 2)
                    <h5 class="mb-3 text-info"><i class="ft-dollar-sign"></i> {{ __('products.pricing_inventory') }}</h5>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>{{ __('products.sku') }} <span class="text-danger">*</span></label>
                            <input type="text" wire:model="sku" class="form-control">
                            @error('sku') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label>{{ __('products.has_variants') }}</label>
                            <select wire:model.live="has_variants" class="form-control">
                                <option value="0">{{ __('products.no') }}</option>
                                <option value="1">{{ __('products.yes') }}</option>
                            </select>
                        </div>

                        @if(!$has_variants)
                            <div class="col-md-6 form-group">
                                <label>{{ __('products.price') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" wire:model="price" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('products.manage_stock') }}</label>
                                <select wire:model.live="manage_stock" class="form-control">
                                    <option value="0">{{ __('products.no') }}</option>
                                    <option value="1">{{ __('products.yes') }}</option>
                                </select>
                            </div>
                            @if($manage_stock)
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.quantity') }}</label>
                                    <input type="number" wire:model="quantity" class="form-control">
                                </div>
                            @endif
                        @else
                            {{-- إدارة المتغيرات --}}
                            <div class="col-md-12 mt-3">
                                <h6 class="text-primary font-weight-bold">{{ __('products.manage_variants') }}</h6>
                                @foreach ($variantRows as $i)
                                    <div class="row bg-light p-2 mb-3 border border-secondary rounded align-items-center position-relative">
                                        
                                        {{-- زر مسح المتغير --}}
                                        @if(count($variantRows) > 1)
                                            <button type="button" wire:click="removeVariant({{ $i }})" class="btn btn-sm btn-danger position-absolute" style="top: -10px; right: -10px; border-radius: 50%;"><i class="ft-trash-2"></i></button>
                                        @endif

                                        <div class="col-md-2 form-group mb-0">
                                            <label class="font-weight-bold">{{ __('products.price') }} <span class="text-danger">*</span></label>
                                            <input type="number" wire:model="prices.{{ $i }}" class="form-control form-control-sm">
                                        </div>

                                        <div class="col-md-2 form-group mb-0">
                                            <label class="font-weight-bold">{{ __('products.quantity') }} <span class="text-danger">*</span></label>
                                            <input type="number" wire:model="quantities.{{ $i }}" class="form-control form-control-sm">
                                        </div>

                                        @foreach ($this->productAttributes as $attribute)
                                            <div class="col-md-3 form-group mb-0">
                                                <label class="font-weight-bold">{{ $attribute->getTranslation('name', app()->getLocale()) ?? $attribute->name }}</label>
                                                <select wire:model.live="attribute_values.{{ $i }}.{{ $attribute->id }}" class="form-control form-control-sm">
                                                    <option value="">{{ __('products.select') }}</option>
                                                    @foreach ($attribute->values as $val)
                                                        <option value="{{ $val->id }}">{{ $val->getTranslation('value', app()->getLocale()) ?? $val->value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach

                                        {{-- صور المتغير (الحالية والجديدة) --}}
                                        <div class="col-md-12 mt-3 border-top pt-3">
                                            <label class="font-weight-bold">{{ __('products.variant_images') }}</label>
                                            <input type="file" wire:model="newVariantImages.{{ $i }}" class="form-control-file" multiple accept="image/*">
                                            
                                            <div wire:loading wire:target="newVariantImages.{{ $i }}" class="text-info mt-1 font-small-3">
                                                <i class="ft-loader spinner"></i> جاري الرفع...
                                            </div>

                                            <div class="row mt-2">
                                                {{-- الصور القديمة --}}
                                                @if(isset($existingVariantImages[$i]))
                                                    @foreach($existingVariantImages[$i] as $imgId => $imgName)
                                                        <div class="col-md-2 col-sm-3 col-4 mb-2 position-relative">
                                                            <img src="{{ Storage::url('variants/' . $imgName) }}" class="img-thumbnail rounded shadow-sm" style="height: 80px; width: 100%; object-fit: cover; border: 2px solid #ccc;">
                                                            <button type="button" wire:click="removeExistingVariantImage({{ $i }}, {{ $imgId }})" class="btn btn-danger btn-sm position-absolute" style="top: -5px; right: 5px; padding: 2px 6px; border-radius: 50%;"><i class="ft-x"></i></button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                
                                                {{-- الصور الجديدة المرفوعة --}}
                                                @if(isset($newVariantImages[$i]))
                                                    @foreach($newVariantImages[$i] as $imgIndex => $img)
                                                        <div class="col-md-2 col-sm-3 col-4 mb-2 position-relative">
                                                            <img src="{{ $img->temporaryUrl() }}" class="img-thumbnail rounded shadow-sm border-info" style="height: 80px; width: 100%; object-fit: cover;">
                                                            <button type="button" wire:click="removeNewVariantImage({{ $i }}, {{ $imgIndex }})" class="btn btn-danger btn-sm position-absolute" style="top: -5px; right: 5px; padding: 2px 6px; border-radius: 50%;"><i class="ft-x"></i></button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                                
                                <button type="button" wire:click="addVariant" class="btn btn-sm btn-info mt-2"><i class="ft-plus"></i> {{ __('products.add_new_variant') }}</button>
                            </div>
                        @endif

                        {{-- الخصومات --}}
                        <div class="col-md-12 form-group mt-3 border-top pt-3">
                            <label>{{ __('products.has_discount') }}</label>
                            <select wire:model.live="has_discount" class="form-control">
                                <option value="0">{{ __('products.no') }}</option>
                                <option value="1">{{ __('products.yes') }}</option>
                            </select>
                        </div>
                        @if($has_discount)
                            <div class="col-md-4 form-group">
                                <label>{{ __('products.discount_percentage') }}</label>
                                <input type="number" wire:model="discount_percentage" class="form-control" placeholder="%">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('products.start_discount') }}</label>
                                <input type="date" wire:model="start_discount" class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('products.end_discount') }}</label>
                                <input type="date" wire:model="end_discount" class="form-control">
                            </div>
                        @endif
                    </div>
                    <div class="text-right mt-4">
                        <button type="button" wire:click="previousStep" class="btn btn-warning mr-2 px-4"><i class="ft-chevron-right"></i> {{ __('products.previous') }}</button>
                        <button type="button" wire:click="nextStep" class="btn btn-info px-4">{{ __('products.next') }} <i class="ft-chevron-left"></i></button>
                    </div>
                @endif


                {{-- ---------------- الخطوة 3 (الصور الأساسية للمنتج) ---------------- --}}
                @if($currentStep == 3)
                    <h5 class="mb-3 text-info"><i class="ft-image"></i> {{ __('products.product_images') }}</h5>
                    
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">{{ __('products.upload_new_images') }}</label>
                            <input type="file" wire:model="newProductImages" class="form-control-file" multiple accept="image/*">
                            
                            <div wire:loading wire:target="newProductImages" class="text-info mt-1 font-small-3">
                                <i class="ft-loader spinner"></i> جاري الرفع...
                            </div>
                        </div>
                    </div>

                    {{-- عرض الصور --}}
                    <div class="row mt-3">
                        {{-- الصور القديمة --}}
                        @if(!empty($existingProductImages))
                            <div class="col-12"><h6 class="text-muted">الصور الحالية:</h6></div>
                            @foreach($existingProductImages as $imgId => $imgName)
                                <div class="col-md-2 col-sm-3 col-4 mb-3 position-relative">
                                    <img src="{{ Storage::url('products/' . $imgName) }}" class="img-thumbnail rounded shadow-sm" style="height: 120px; width: 100%; object-fit: cover; border: 2px solid #ccc;">
                                    <button type="button" wire:click="removeExistingProductImage({{ $imgId }})" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 20px; padding: 2px 6px; border-radius: 50%;"><i class="ft-trash-2"></i></button>
                                </div>
                            @endforeach
                        @endif

                        {{-- الصور الجديدة --}}
                        @if(!empty($newProductImages))
                            <div class="col-12 mt-2"><h6 class="text-success">الصور الجديدة المضافة:</h6></div>
                            @foreach($newProductImages as $index => $img)
                                <div class="col-md-2 col-sm-3 col-4 mb-3 position-relative">
                                    <img src="{{ $img->temporaryUrl() }}" class="img-thumbnail rounded shadow-sm border-info" style="height: 120px; width: 100%; object-fit: cover;">
                                    <button type="button" wire:click="removeNewProductImage({{ $index }})" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 20px; padding: 2px 6px; border-radius: 50%;"><i class="ft-x"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="text-right mt-4">
                        <button type="button" wire:click="previousStep" class="btn btn-warning mr-2 px-4"><i class="ft-chevron-right"></i> {{ __('products.previous') }}</button>
                        <button type="submit" class="btn btn-success px-4">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                            <i class="ft-save"></i> {{ __('products.save_product') }}
                        </button>
                    </div>
                @endif
                
            </form>
        </div>
    </div>
</div>