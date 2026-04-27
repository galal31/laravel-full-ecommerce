@extends('layouts.dashboard.master')
@section('title', __('products.product_details') . ' | ' . $product->name)

@section('css')
<style>
    :root {
        --page-bg: #f5f7fb;
        --card-bg: #ffffff;
        --border-color: #e6ebf2;
        --text-main: #2f3b52;
        --text-muted: #7b8798;
        --primary: #5b73e8;
        --primary-soft: rgba(91, 115, 232, 0.10);
        --success-soft: rgba(52, 195, 143, 0.12);
        --danger-soft: rgba(244, 106, 106, 0.12);
        --success: #34c38f;
        --danger: #f46a6a;
        --shadow-soft: 0 8px 24px rgba(15, 23, 42, 0.05);
        --radius-lg: 18px;
        --radius-md: 14px;
        --radius-sm: 10px;
    }

    body {
        background: var(--page-bg) !important;
        color: var(--text-main);
    }

    .content-body {
        padding-top: 10px;
    }

    .product-show-page {
        padding: 10px 0 30px;
    }

    .product-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 24px;
        height: 100%;
    }

    .gallery-card {
        padding: 20px;
    }

    .img-container {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 14px;
        max-width: 380px;
        margin: 0 auto;
    }

    .product-main-img {
        width: 100%;
        height: 280px;
        object-fit: contain;
        display: block;
        border-radius: 12px;
        transition: opacity 0.25s ease;
    }

    .thumbs-wrapper {
        max-width: 380px;
        margin: 14px auto 0;
        gap: 10px;
    }

    .product-thumb {
        width: 62px;
        height: 62px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        background: #fff;
        transition: all 0.25s ease;
        padding: 2px;
    }

    .product-thumb:hover,
    .product-thumb.active {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(91, 115, 232, 0.14);
    }

    .page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 18px;
    }

    .page-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .product-name {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    .product-subtitle {
        color: var(--text-muted);
        font-size: 0.98rem;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .soft-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.83rem;
        font-weight: 600;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .soft-badge.primary {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: rgba(91, 115, 232, 0.14);
    }

    .soft-badge.success {
        background: var(--success-soft);
        color: var(--success);
        border-color: rgba(52, 195, 143, 0.18);
    }

    .soft-badge.danger {
        background: var(--danger-soft);
        color: var(--danger);
        border-color: rgba(244, 106, 106, 0.18);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 24px;
        margin-bottom: 24px;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 14px 16px;
    }

    .info-label {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 6px;
        display: block;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
        word-break: break-word;
    }

    .tags-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag-chip {
        background: #f1f5ff;
        color: var(--primary);
        border: 1px solid #dbe4ff;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 0.82rem;
        font-weight: 500;
    }

    .section-divider {
        height: 1px;
        background: var(--border-color);
        margin: 24px 0;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 14px;
    }

    .price-stock-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 18px;
    }

    .price-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }

    .soft-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: #fff;
    }

    .soft-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .soft-table thead th {
        background: #f8fafc;
        color: #5b6577;
        font-weight: 700;
        font-size: 0.88rem;
        border-bottom: 1px solid var(--border-color);
        padding: 14px;
        text-align: center;
        white-space: nowrap;
    }

    .soft-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #edf1f7;
        vertical-align: middle;
        text-align: center;
        color: var(--text-main);
        background: #fff;
    }

    .soft-table tbody tr:last-child td {
        border-bottom: none;
    }

    .desc-box {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 16px;
        color: #556070;
        line-height: 1.9;
    }

    .actions-wrap {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        flex-wrap: wrap;
    }

    .btn-soft {
        min-width: 130px;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
        font-size: 0.92rem;
        transition: 0.25s ease;
    }

    .btn-soft-back {
        background: #fff;
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }

    .btn-soft-back:hover {
        background: #f8fafc;
    }

    .btn-soft-primary {
        background: var(--primary);
        color: #fff;
        border: 1px solid var(--primary);
    }

    .btn-soft-primary:hover {
        background: #4f67db;
        border-color: #4f67db;
        color: #fff;
    }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 60px;
        margin-bottom: 14px;
        display: block;
        opacity: 0.5;
    }

    @media (max-width: 991.98px) {
        .product-main-img {
            height: 240px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .product-card {
            padding: 18px;
        }
    }

    @media (max-width: 767.98px) {
        .page-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-name {
            font-size: 1.35rem;
        }

        .price-stock-box {
            flex-direction: column;
            align-items: flex-start;
        }

        .actions-wrap {
            justify-content: stretch;
        }

        .actions-wrap .btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .gallery-card {
            padding: 14px;
        }

        .img-container {
            max-width: 100%;
        }

        .product-main-img {
            height: 210px;
        }

        .product-thumb {
            width: 54px;
            height: 54px;
        }
    }
    .variant-thumb {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        margin: 2px;
        transition: transform 0.2s ease;
    }
    .variant-thumb:hover {
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
<div class="content-body">
    <div class="product-show-page">
        <div class="page-head">
            <h4 class="page-title">{{ __('products.product_details') }}</h4>
            <a href="{{ route('dashboard.products.index') }}" class="btn btn-sm btn-soft btn-soft-back">
                <i class="ft-arrow-right"></i> {{ __('products.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 mb-4">
                <div class="product-card gallery-card text-center">
                    @if($product->images->count() > 0)
                        <div class="img-container">
                            <img
                                src="{{ asset('storage/products/' . $product->images->first()->file_name) }}"
                                id="mainImage"
                                class="product-main-img"
                                alt="{{ $product->name }}"
                            >
                        </div>

                        <div class="thumbs-wrapper d-flex justify-content-center flex-wrap">
                            @foreach($product->images as $index => $image)
                                <img
                                    src="{{ asset('storage/products/' . $image->file_name) }}"
                                    class="product-thumb {{ $index == 0 ? 'active' : '' }}"
                                    onclick="changeImage(this, '{{ asset('storage/products/' . $image->file_name) }}')"
                                    alt="{{ $product->name }}"
                                >
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ft-image"></i>
                            <p class="mb-0">{{ __('products.no_images') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-8 col-xl-9">
                <div class="product-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h2 class="product-name">{{ $product->name }}</h2>
                            <p class="product-subtitle">{{ $product->small_desc }}</p>
                        </div>

                        <span class="soft-badge {{ $product->status ? 'success' : 'danger' }}">
                            {{ $product->status ? __('products.active') : __('products.inactive') }}
                        </span>
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <span class="info-label">{{ __('products.category') }}</span>
                            <div class="info-value">{{ $product->category->name ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <span class="info-label">{{ __('products.brand') }}</span>
                            <div class="info-value">{{ $product->brand->name ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <span class="info-label">{{ __('products.sku') }}</span>
                            <div class="info-value">
                                <span class="soft-badge primary">{{ $product->sku }}</span>
                            </div>
                        </div>

                        <div class="info-box">
                            <span class="info-label">{{ __('products.views') }}</span>
                            <div class="info-value">{{ $product->views }}</div>
                        </div>
                    </div>

                    @if($product->tags->count() > 0)
                        <div class="mb-4">
                            <h5 class="section-title mb-3">{{ __('products.tags') }}</h5>
                            <div class="tags-wrap">
                                @foreach($product->tags as $tag)
                                    <span class="tag-chip">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="section-divider"></div>

                    @if($product->variants->isNotEmpty())
                        <h5 class="section-title">{{ __('products.product_variants') }}</h5>

                        <div class="soft-table-wrap">
                            <table class="soft-table">
    <thead>
        <tr>
            <th>{{ __('products.attributes') }}</th>
            <th>{{ __('products.images') }}</th> <th>{{ __('products.price') }}</th>
            <th>{{ __('products.stock') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->variants as $variant)
            <tr>
                <td>
                    @foreach($variant->attributeValues as $attrValue)
                        <span class="soft-badge primary mb-1">
                            {{ $attrValue->attribute->name ?? '' }} : {{ $attrValue->value ?? '' }}
                        </span>
                    @endforeach
                </td>
                
                <td>
                    @if($variant->images && $variant->images->count() > 0)
                        <div class="d-flex justify-content-center flex-wrap">
                            @foreach($variant->images as $vImage)
                                <a href="{{ asset('storage/variants/' . $vImage->file_name) }}" target="_blank">
                                    <img src="{{ asset('storage/variants/' . $vImage->file_name) }}" 
                                         alt="Variant" 
                                         class="variant-thumb"
                                         title="اضغط لعرض الصورة بحجمها الطبيعي">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted font-small-3">لا توجد صور</span>
                    @endif
                </td>

                <td class="font-weight-bold">{{ $variant->price }}</td>
                <td>
                    <span class="soft-badge {{ $variant->stock > 0 ? 'success' : 'danger' }}">
                        {{ $variant->stock }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
                        </div>
                    @else
                        <div class="price-stock-box">
                            <div>
                                <span class="info-label d-block">{{ __('products.price') }}</span>
                                <h3 class="price-value">{{ $product->price }}</h3>
                            </div>

                            <div>
                                <span class="info-label d-block">{{ __('products.stock') }}</span>
                                @if($product->manage_stock)
                                    <span class="soft-badge {{ $product->quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $product->quantity ?? 0 }}
                                    </span>
                                @else
                                    <span class="soft-badge primary">{{ __('products.unlimited') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($product->desc)
                        <div class="mt-4">
                            <h5 class="section-title">{{ __('products.description') }}</h5>
                            <div class="desc-box">
                                {!! $product->desc !!}
                            </div>
                        </div>
                    @endif

                    <div class="actions-wrap">
                        <a href="{{ route('dashboard.products.index') }}" class="btn btn-soft btn-soft-back">
                            {{ __('products.back') }}
                        </a>
                        <a href="{{ route('dashboard.products.edit', $product->id) }}" class="btn btn-soft btn-soft-primary">
                            <i class="ft-edit mr-1"></i> {{ __('products.edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeImage(element, src) {
        const mainImg = document.getElementById('mainImage');
        if (!mainImg) return;

        mainImg.style.opacity = 0;

        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = 1;
        }, 150);

        document.querySelectorAll('.product-thumb').forEach(img => {
            img.classList.remove('active');
        });

        element.classList.add('active');
    }
</script>
@endsection