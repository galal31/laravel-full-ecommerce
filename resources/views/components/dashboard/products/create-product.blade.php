<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\Tag;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Attribute;
use App\Models\Dashboard\ProductVariant;
use App\Traits\UploadFileTrait;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use App\Services\dashboard\ProductService;
new class extends Component {
    use WithFileUploads, UploadFileTrait;

    public $currentStep = 1;

    public $name = [];
    public $small_desc = [];
    public $desc = [];

    public $sku, $category_id, $brand_id, $available_for, $tags, $price, $quantity;
    public $discount, $start_discount, $end_discount;

    public $has_variants = 0;
    public $manage_stock = 0;
    public $has_discount = 0;

    public $value_row_count = 1;
    public $prices = [];
    public $quantities = [];
    public $attribute_values = [];

    public $images = [];
    public $variantImages = [];
    // الدالة دي محطوط عليها #[Computed] عشان متتعبش السيرفر رايح جاي.
    // بتضرب الغطس في الداتابيز مرة واحدة بس تجيب الداتا وتشيلها في جيبها (الميموري).
    // ندهتها في الـ Blade مرة أو ألف، هي شغالة على الكاش القديم لحد ما الريكويست يخلص.
    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    #[Computed]
    public function brands()
    {
        return Brand::all();
    }

    #[Computed]
    public function productAttributes()
    {
        return Attribute::with('values')->get();
    }

    public function addNewVariant()
    {
        $this->value_row_count++;
    }


    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'name.ar' => ['required', 'string', 'max:80'],
                'name.en' => ['required', 'string', 'max:80'],
                'small_desc.ar' => ['required', 'string', 'max:150'],
                'small_desc.en' => ['required', 'string', 'max:150'],
                'desc.ar' => ['required', 'string', 'max:1000'],
                'desc.en' => ['required', 'string', 'max:1000'],
                'category_id' => ['required', 'exists:categories,id'],
                'brand_id' => ['nullable', 'exists:brands,id'],
                'available_for' => ['nullable', 'date'],
            ]);
        } elseif ($this->currentStep == 2) {
            // die dump every thing
            // dd($this);
            $rules = [
                'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
                'has_variants' => ['required', 'in:0,1'],
                'has_discount' => ['required', 'in:0,1'],
            ];

            if ($this->has_variants == 0) {
                $rules['manage_stock'] = ['required', 'in:0,1'];
                $rules['price'] = ['required', 'numeric', 'min:1'];
                if ($this->manage_stock == 1) {
                    $rules['quantity'] = ['required', 'numeric', 'min:1'];
                }
            } else {
                $rules['prices'] = ['required', 'array', 'min:1'];
                $rules['prices.*'] = ['required', 'numeric', 'min:1'];
                $rules['quantities'] = ['required', 'array', 'min:1'];
                $rules['quantities.*'] = ['required', 'integer', 'min:0'];
                $rules['attribute_values'] = ['required', 'array'];
                $rules['attribute_values.*.*'] = ['required', 'numeric'];
                $rules['variantImages'] = ['nullable', 'array'];
                $rules['variantImages.*.*'] = ['image', 'mimes:jpeg,png,jpg', 'max:2048'];
            }

            if ($this->has_discount == 1) {
                $rules['discount'] = ['required', 'numeric', 'min:1'];
                $rules['start_discount'] = ['required', 'date'];
                $rules['end_discount'] = ['required', 'date', 'after_or_equal:start_discount'];
            }

            $this->validate($rules);
        } elseif ($this->currentStep == 3) {
            $this->validate([
                'images' => ['required', 'array', 'min:1'],
                'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ]);
        }
        $this->currentStep++;
    }

    public function removeVariantImage($variantIndex, $imageIndex)
    {
        if (isset($this->variantImages[$variantIndex][$imageIndex])) {
            unset($this->variantImages[$variantIndex][$imageIndex]);
        }
    }

    // قم بتعديل دالة removeVariant لمسح صور المتغير لو تم حذفه
    public function removeVariant()
    {
        if ($this->value_row_count > 1) {
            $this->value_row_count--;
            array_pop($this->prices);
            array_pop($this->quantities);
            array_pop($this->attribute_values);

            // حذف صور المتغير الأخير إذا كانت موجودة
            if (isset($this->variantImages[$this->value_row_count])) {
                unset($this->variantImages[$this->value_row_count]);
            }
        }
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
    }

    public function submitForm(ProductService $productService)
    {
        // 1. تجميع الداتا في مصفوفة (Array)
        $productData = [
            'name' => $this->name,
            'small_desc' => $this->small_desc,
            'desc' => $this->desc,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'sku' => $this->sku,
            'available_for' => $this->available_for,
            'tags' => $this->tags,
            'has_variants' => $this->has_variants,
            'manage_stock' => $this->manage_stock,
            'has_discount' => $this->has_discount,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'start_discount' => $this->start_discount,
            'end_discount' => $this->end_discount,
            'prices' => $this->prices,
            'quantities' => $this->quantities,
            'attribute_values' => $this->attribute_values,
            'variantImages' => $this->variantImages,
        ];

        try {
            // 2. نبعت الداتا والصور للـ Service عشان يتصرف
            $productService->storeProduct($productData, $this->images);

            // 3. تصفير الفورم ورسالة النجاح
            $this->reset();
            $this->currentStep = 1;
            session()->flash('success', __('products.product_created_successfully'));
        } catch (\Exception $e) {
            // 4. لو حصل أي خطأ في الـ Service هنيجي هنا
            session()->flash('error', __('products.error_occurred') . ' ' . $e->getMessage());
        }
    }
};
?>

<div class="content-body">
    <section class="flexbox-container">
        <div class="col-12">
            <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('products.add_new_product') }}</h4>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success m-2">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger m-2">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card-content">
                    <div class="card-body">

                        {{-- شريط التقدم (Progress Bar) --}}
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <div class="text-center mx-2">
                                <span
                                    class="badge {{ $currentStep >= 1 ? 'badge-primary' : 'badge-secondary' }} px-2 py-1 mb-1"
                                    style="font-size: 1.1rem;">1</span>
                                <p class="mb-0 text-bold-600">{{ __('products.step_1') }}</p>
                            </div>
                            <div class="border-top border-2 {{ $currentStep >= 2 ? 'border-primary' : 'border-secondary' }} flex-grow-1 mx-2"
                                style="max-width: 100px;"></div>

                            <div class="text-center mx-2">
                                <span
                                    class="badge {{ $currentStep >= 2 ? 'badge-primary' : 'badge-secondary' }} px-2 py-1 mb-1"
                                    style="font-size: 1.1rem;">2</span>
                                <p class="mb-0 text-bold-600">{{ __('products.step_2') }}</p>
                            </div>
                            <div class="border-top border-2 {{ $currentStep >= 3 ? 'border-primary' : 'border-secondary' }} flex-grow-1 mx-2"
                                style="max-width: 100px;"></div>

                            <div class="text-center mx-2">
                                <span
                                    class="badge {{ $currentStep == 3 ? 'badge-primary' : 'badge-secondary' }} px-2 py-1 mb-1"
                                    style="font-size: 1.1rem;">3</span>
                                <p class="mb-0 text-bold-600">{{ __('products.step_3') }}</p>
                            </div>
                        </div>
                        <hr>

                        {{-- ========================================== --}}
                        {{-- الخطوة الأولى: البيانات الأساسية --}}
                        {{-- ========================================== --}}
                        <div class="{{ $currentStep != 1 ? 'd-none' : '' }}">
                            <h5 class="form-section text-primary"><i class="ft-info"></i>
                                {{ __('products.basic_info') }}</h5>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.name_ar') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name.ar" class="form-control">
                                    @error('name.ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.name_en') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name.en" class="form-control">
                                    @error('name.en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.category') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="category_id" class="form-control">
                                        <option value="">{{ __('products.select_category') }}</option>
                                        @foreach ($this->categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->getTranslation('name', app()->getLocale()) ?? $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.brand') }}</label>
                                    <select wire:model.live="brand_id" class="form-control">
                                        <option value="">{{ __('products.select_brand') }}</option>
                                        @foreach ($this->brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ $brand->getTranslation('name', app()->getLocale()) ?? $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.small_desc_ar') }} <span
                                            class="text-danger">*</span></label>
                                    <textarea wire:model="small_desc.ar" rows="2" class="form-control"></textarea>
                                    @error('small_desc.ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.small_desc_en') }} <span
                                            class="text-danger">*</span></label>
                                    <textarea wire:model="small_desc.en" rows="2" class="form-control"></textarea>
                                    @error('small_desc.en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.desc_ar') }} <span class="text-danger">*</span></label>
                                    <textarea wire:model="desc.ar" rows="4" class="form-control"></textarea>
                                    @error('desc.ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.desc_en') }} <span class="text-danger">*</span></label>
                                    <textarea wire:model="desc.en" rows="4" class="form-control"></textarea>
                                    @error('desc.en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.tags') }}</label>
                                    <input type="text" wire:model="tags" class="form-control"
                                        placeholder="{{ __('products.tags_placeholder') }}">
                                    @error('tags')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('products.available_for') }}</label>
                                    <input type="date" wire:model="available_for" class="form-control">
                                    @error('available_for')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-actions text-right mt-2">
                                <button type="button" wire:click="nextStep"
                                    class="btn btn-primary">{{ __('products.next') }} <i
                                        class="ft-chevron-left"></i></button>
                            </div>
                        </div>

                        {{-- ========================================== --}}
                        {{-- الخطوة الثانية: السعر والمخزون --}}
                        {{-- ========================================== --}}
                        <div class="{{ $currentStep != 2 ? 'd-none' : '' }}">
                            <h5 class="form-section text-primary"><i class="ft-dollar-sign"></i>
                                {{ __('products.pricing_inventory') }}</h5>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>{{ __('products.sku') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="sku" class="form-control"
                                        placeholder="PRD-12345">
                                    @error('sku')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row bg-light p-2 mb-2 rounded">
                                <div class="col-md-4 form-group text-center">
                                    <label class="font-weight-bold">{{ __('products.has_variants') }}</label>
                                    <select wire:model.live="has_variants" class="form-control border-primary">
                                        <option value="0">{{ __('products.no') }}</option>
                                        <option value="1">{{ __('products.yes') }}</option>
                                    </select>
                                    @error('has_variants')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if (!$has_variants)
                                    <div class="col-md-4 form-group text-center">
                                        <label class="font-weight-bold">{{ __('products.manage_stock') }}</label>
                                        <select wire:model.live="manage_stock" class="form-control border-primary">
                                            <option value="0">{{ __('products.no') }}</option>
                                            <option value="1">{{ __('products.yes') }}</option>
                                        </select>
                                        @error('manage_stock')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                                <div class="col-md-4 form-group text-center">
                                    <label class="font-weight-bold">{{ __('products.has_discount') }}</label>
                                    <select wire:model.live="has_discount" class="form-control border-primary">
                                        <option value="0">{{ __('products.no') }}</option>
                                        <option value="1">{{ __('products.yes') }}</option>
                                    </select>
                                    @error('has_discount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if (!$has_variants)
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>{{ __('products.price') }} <span class="text-danger">*</span></label>
                                        <input type="number" wire:model="price" class="form-control">
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if ($manage_stock)
                                        <div class="col-md-6 form-group">
                                            <label>{{ __('products.quantity') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" wire:model="quantity" class="form-control">
                                            @error('quantity')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @php
                                echo $this->value_row_count;
                            @endphp
                            @if ($has_variants)
                                <div class="variants-section mt-3">
                                    <hr>
                                    <h6 class="text-info font-weight-bold"><i class="ft-layers"></i>
                                        {{ __('products.manage_variants') }}</h6>

                                    @for ($i = 0; $i < $value_row_count; $i++)
    <div class="row bg-white p-2 mb-2 border border-info rounded align-items-center">
        <div class="col-md-2 form-group mb-0">
            <label class="font-weight-bold">{{ __('products.price') }} <span class="text-danger">*</span></label>
            <input type="number" wire:model="prices.{{ $i }}" class="form-control form-control-sm">
            @error('prices.' . $i) <span class="text-danger font-small-2">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-2 form-group mb-0">
            <label class="font-weight-bold">{{ __('products.quantity') }} <span class="text-danger">*</span></label>
            <input type="number" wire:model="quantities.{{ $i }}" class="form-control form-control-sm">
            @error('quantities.' . $i) <span class="text-danger font-small-2">{{ $message }}</span> @enderror
        </div>

        @foreach ($this->productAttributes as $attribute)
            <div class="col-md-3 form-group mb-0">
                <label class="font-weight-bold">{{ $attribute->getTranslation('name', app()->getLocale()) ?? $attribute->name }} <span class="text-danger">*</span></label>
                <select wire:model.live="attribute_values.{{ $i }}.{{ $attribute->id }}" class="form-control form-control-sm">
                    <option value="">{{ __('products.select') }}</option>
                    @if ($attribute->values)
                        @foreach ($attribute->values as $val)
                            <option value="{{ $val->id }}">
                                {{ $val->getTranslation('value', app()->getLocale()) ?? $val->value }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('attribute_values.' . $i . '.' . $attribute->id) 
                    <span class="text-danger font-small-2">{{ $message }}</span> 
                @enderror
            </div>
        @endforeach

        <div class="col-md-12 mt-3 border-top pt-3">
            <label class="font-weight-bold">{{ __('products.variant_images') }} (اختياري)</label>
            <input type="file" wire:model="variantImages.{{ $i }}" class="form-control-file" multiple accept="image/*">
            
            <div wire:loading wire:target="variantImages.{{ $i }}" class="text-info mt-1 font-small-3">
                <i class="ft-loader spinner"></i> جاري الرفع...
            </div>

            @if(isset($variantImages[$i]) && count($variantImages[$i]) > 0)
                <div class="row mt-2">
                    @foreach($variantImages[$i] as $imgIndex => $img)
                        <div class="col-md-2 col-sm-3 col-4 mb-2 position-relative">
                            <img src="{{ $img->temporaryUrl() }}" class="img-thumbnail rounded shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                            <button type="button" wire:click="removeVariantImage({{ $i }}, {{ $imgIndex }})" class="btn btn-danger btn-sm position-absolute" style="top: -5px; right: 5px; padding: 2px 6px; border-radius: 50%;">
                                <i class="ft-x"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        </div>
@endfor

                                    <div class="mt-2">
                                        <button type="button" wire:click="addNewVariant"
                                            class="btn btn-sm btn-info">
                                            <i class="ft-plus"></i> {{ __('products.add_new_variant') }}
                                        </button>
                                        @if ($value_row_count > 1)
                                            <button type="button" wire:click="removeVariant"
                                                class="btn btn-sm btn-danger">
                                                <i class="ft-trash-2"></i> {{ __('products.remove_last_variant') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($has_discount)
                                <div class="row mt-3">
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('products.discount_percentage') }}</label>
                                        <input type="number" wire:model="discount" class="form-control">
                                        @error('discount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('products.start_discount') }}</label>
                                        <input type="date" wire:model="start_discount" class="form-control">
                                        @error('start_discount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('products.end_discount') }}</label>
                                        <input type="date" wire:model="end_discount" class="form-control">
                                        @error('end_discount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="form-actions text-right d-flex justify-content-between mt-2">
                                <button type="button" wire:click="prevStep" class="btn btn-warning"><i
                                        class="ft-chevron-right"></i> {{ __('products.previous') }}</button>
                                <button type="button" wire:click="nextStep"
                                    class="btn btn-primary">{{ __('products.next') }} <i
                                        class="ft-chevron-left"></i></button>
                            </div>
                        </div>

                        {{-- ========================================== --}}
                        {{-- الخطوة الثالثة: المراجعة --}}
                        {{-- ========================================== --}}
                        <div class="{{ $currentStep != 3 ? 'd-none' : '' }}">
                            <h5 class="form-section text-primary"><i class="ft-image"></i>
                                {{ __('products.media_review') }}</h5>

                            <div class="row mb-3">
                                <div class="col-md-12 form-group">
                                    <label>{{ __('products.product_images') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="file" wire:model="images" class="form-control-file" multiple
                                        accept="image/*">
                                    @error('images')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('images.*')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <div wire:loading wire:target="images" class="text-primary mt-1 font-weight-bold">
                                        <i class="ft-loader spinner"></i> {{ __('products.uploading_images') }}
                                    </div>

                                    @if ($images)
                                        <div class="row mt-3">
                                            @foreach ($images as $index => $image)
                                                <div class="col-md-3 col-sm-4 col-6 mb-2 position-relative">
                                                    <img src="{{ $image->temporaryUrl() }}"
                                                        class="img-thumbnail rounded"
                                                        style="height: 120px; width: 100%; object-fit: cover;">
                                                    <button type="button"
                                                        wire:click="removeImage({{ $index }})"
                                                        class="btn btn-danger btn-sm position-absolute"
                                                        style="top: 5px; right: 20px; padding: 2px 6px; border-radius: 50%;">
                                                        <i class="ft-x"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="alert alert-secondary mt-2">
                                <strong><i class="ft-alert-circle"></i> {{ __('products.review_data') }}</strong>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>{{ __('products.name_ar') }}</th>
                                            <th>{{ __('products.sku') }}</th>
                                            <th>{{ __('products.has_variants') }}</th>
                                            <th>{{ __('products.variants_count') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $name[app()->getLocale()] ?? ($name['ar'] ?? '---') }}</td>
                                            <td>{{ $sku ?? '---' }}</td>
                                            <td>{{ $has_variants ? __('products.yes') : __('products.no') }}</td>
                                            <td>{{ $has_variants ? $value_row_count : '---' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-actions text-right d-flex justify-content-between mt-3">
                                <button type="button" wire:click="prevStep" class="btn btn-warning"><i
                                        class="ft-chevron-right"></i> {{ __('products.previous') }}</button>
                                <button type="button" wire:click="submitForm" class="btn btn-success"><i
                                        class="ft-save"></i> {{ __('products.save_product') }}</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
