@extends('layouts.website.userlayout')

@section('title', 'Shopus | Become A Vendor')

@section('content')
    <section id="hero" class="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper hero-wrapper">

            @foreach ($sliders as $slider)

                <div class="swiper-slide hero-slider-one"
                     style="background-image: url('{{ $slider->file_name }}'); background-size: cover; background-position: center;">
                    <div class="container">
                        <div class="col-lg-6">
                            <div class="wrapper-section" data-aos="fade-up">
                                <div class="wrapper-info">

                                    <h5 class="wrapper-subtitle">
                                        {{ $slider->note }}
                                    </h5>

                                    <h1 class="wrapper-details">
                                        {{ $slider->note }}
                                    </h1>

                                    <a href="{{ $slider->link ?? '#' }}" class="shop-btn">
                                        Shop Now
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <div class="swiper-pagination"></div>
    </div>
</section>


   
@endsection
