@extends('layouts.dashboard.master')
@section('title', __('attributes.attributes_and_variants'))

@section('content')
<div class="content-body">
    <section class="flexbox-container">
        <div class="col-12">
            <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h4 class="card-title mb-2 mb-md-0">Attributes List</h4>
                    <button type="button" class="btn btn-outline-info">
                        <i class="ft-plus"></i> Add New Attribute
                    </button>
                </div>

                @livewire('dashboard.products.edit-product',[
                    'productId' => $product->id
                ]);
            </div>
        </div>
    </section>
</div>



@endsection