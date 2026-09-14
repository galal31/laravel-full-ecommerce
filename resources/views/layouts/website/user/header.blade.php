<header id="header" class="header">
    <div class="header-top-section">
        <div class="container">
            <div class="header-top">
                <div class="header-profile">
                    @guest
                        <a href="{{ route('website.login') }}">
                            <span>{{ __('site_header.account') }}</span>
                        </a>
                    @else
                        <form action="{{ route('website.logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="border: 0; background: transparent; padding: 0;">
                                {{ __('site_header.logout') }}
                            </button>
                        </form>
                    @endguest
                    <a href="{{ route('website.faqs.index') }}"><span>{{ __('site_header.support') }}</span></a>
                </div>
                <div class="header-profile">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            <span>{{ strtoupper($localeCode) }}</span>
                        </a>
                    @endforeach
                </div>
                <div class="header-contact d-none d-lg-block">
                    <a href="tel:+006454568">
                        <span>{{ __('site_header.need_help_call_us') }}</span>
                        <span class="contact-number">+ 00645 4568</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="header-center-section d-none d-lg-block">
        <div class="container">
            <div class="header-center">
                <div class="logo">
                    <a href="{{ route('website.home') }}">
                        <img src="{{ asset('website-assets/assets/images/logos/logo.webp') }}" alt="logo">
                    </a>
                </div>
                <div class="header-cart-items">
                    <div class="header-search">
                        <button class="header-search-btn" onclick="modalAction('.search')">
                            <span>
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.9708 16.4151C12.5227 17.4021 10.9758 17.9723 9.27353 18.0062C5.58462 18.0802 2.75802 16.483 1.05056 13.1945C-1.76315 7.77253 1.33485 1.37571 7.25086 0.167548C12.2281 -0.848249 17.2053 2.87895 17.7198 7.98579C17.9182 9.95558 17.5566 11.7939 16.5852 13.5061C16.4512 13.742 16.483 13.8725 16.6651 14.0553C18.2412 15.6386 19.8112 17.2272 21.3735 18.8244C22.1826 19.6513 22.2058 20.7559 21.456 21.4932C20.7697 22.1678 19.7047 22.1747 18.9764 21.4793C18.3623 20.8917 17.7774 20.2737 17.1796 19.6688C16.118 18.5929 15.0564 17.5153 13.9708 16.4151ZM2.89545 9.0364C2.91692 12.4172 5.59664 15.1164 8.91967 15.1042C12.2384 15.092 14.9138 12.3493 14.8889 8.98505C14.864 5.63213 12.1826 2.92508 8.89047 2.92857C5.58204 2.93118 2.87397 5.68958 2.89545 9.0364Z"
                                        fill="black" />
                                </svg>
                            </span>
                        </button>
                        <div class="modal-wrapper search">
                            <div onclick="modalAction('.search')" class="anywhere-away"></div>

                            <div class="modal-main">
                                <div class="wrapper-close-btn" onclick="modalAction('.search')">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="red" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="wrapper-main">
                                    <div class="search-section">
                                        <input type="text" placeholder="{{ __('site_header.search_products') }}">
                                        <div class="divider"></div>
                                        <button type="button">{{ __('site_header.all_categories') }}</button>
                                        <button type="button" class="shop-btn" disabled>
                                            {{ __('site_header.search') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @if (Route::has('website.compare.index'))
                    <div class="header-compaire">
                        <a href="{{ route('website.compare.index') }}" class="cart-item">
                            <span>
                                <svg width="34" height="27" viewBox="0 0 34 27" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M22 16.0094C21.997 22.0881 17.0653 27.007 10.9802 27C4.90444 26.9931 -0.00941233 22.0569 1.3538e-05 15.9688C0.00943941 9.89602 4.95157 4.98663 11.0422 5.00003C17.0961 5.01342 22.003 9.94315 22 16.0094ZM6.16553 15.7812C6.40365 12.6236 8.72192 11.2861 10.5868 11.1993C12.3305 11.1179 14.4529 12.3353 14.7465 13.6143C14.2425 13.6143 13.7459 13.6143 13.2429 13.6143C13.2429 14.0241 13.2429 14.3986 13.2429 14.7975C14.308 14.7975 15.3374 14.8064 16.3668 14.793C16.7805 14.7876 17.0102 14.5291 17.0147 14.1005C17.0221 13.3414 17.0172 12.5824 17.0172 11.8234C17.0172 11.558 17.0172 11.2925 17.0172 11.0311C16.5836 11.0311 16.2165 11.0311 15.7908 11.0311C15.7908 11.6046 15.7908 12.1572 15.7908 12.7937C13.9379 10.0444 10.8447 9.4545 8.48578 10.4824C6.21811 11.4706 4.90792 13.847 5.04682 15.7817C5.40997 15.7812 5.77609 15.7812 6.16553 15.7812ZM15.8191 16.2178C15.7581 17.4576 15.3498 18.547 14.4742 19.4286C13.5976 20.3111 12.5265 20.772 11.2858 20.8008C9.57472 20.8405 7.568 19.6424 7.2495 18.3892C7.75403 18.3892 8.25013 18.3892 8.76012 18.3892C8.76012 17.9809 8.76012 17.6064 8.76012 17.2041C7.68458 17.2041 6.64178 17.1921 5.59997 17.21C5.19962 17.2169 5.00069 17.4839 4.99771 17.9442C4.99176 18.803 4.99573 19.6612 4.99573 20.52C4.99573 20.6698 4.99573 20.8196 4.99573 20.964C5.4318 20.964 5.79692 20.964 6.20224 20.964C6.20224 20.3895 6.20224 19.8418 6.20224 19.1686C7.07984 20.4912 8.16976 21.3465 9.58216 21.7617C11.0184 22.1839 12.4114 22.0494 13.7548 21.4035C15.8191 20.4113 17.0946 18.1466 16.9507 16.2178C16.5861 16.2178 16.2209 16.2178 15.8191 16.2178Z"
                                        fill="#6E6D79" />
                                    <path
                                        d="M6.16568 15.7814C5.77624 15.7814 5.41062 15.7814 5.04648 15.7814C4.90757 13.8471 6.21777 11.4703 8.48543 10.482C10.8444 9.45411 13.9376 10.044 15.7905 12.7934C15.7905 12.1569 15.7905 11.6042 15.7905 11.0307C16.2161 11.0307 16.5833 11.0307 17.0168 11.0307C17.0168 11.2917 17.0168 11.5571 17.0168 11.823C17.0168 12.582 17.0218 13.341 17.0144 14.1001C17.0104 14.5287 16.7802 14.7877 16.3665 14.7926C15.3371 14.8055 14.3076 14.7971 13.2425 14.7971C13.2425 14.3982 13.2425 14.0237 13.2425 13.6139C13.7451 13.6139 14.2417 13.6139 14.7462 13.6139C14.4525 12.3355 12.3302 11.118 10.5864 11.1989C8.72207 11.2862 6.4038 12.6237 6.16568 15.7814Z"
                                        fill="white" />
                                    <path
                                        d="M15.8191 16.2178C16.2209 16.2178 16.5865 16.2178 16.9502 16.2178C17.094 18.1466 15.8186 20.4108 13.7543 21.4035C12.4109 22.0494 11.0178 22.1834 9.58161 21.7617C8.16971 21.3469 7.07978 20.4912 6.20169 19.1686C6.20169 19.8418 6.20169 20.3895 6.20169 20.9639C5.79687 20.9639 5.43125 20.9639 4.99518 20.9639C4.99518 20.8201 4.99518 20.6703 4.99518 20.5199C4.99518 19.6612 4.99121 18.8029 4.99716 17.9442C5.00014 17.4838 5.19907 17.2169 5.59943 17.21C6.64173 17.1916 7.68403 17.204 8.75957 17.204C8.75957 17.6064 8.75957 17.9809 8.75957 18.3892C8.25008 18.3892 7.75348 18.3892 7.24895 18.3892C7.56794 19.6428 9.57466 20.8404 11.2852 20.8007C12.526 20.772 13.597 20.3111 14.4736 19.4285C15.3492 18.547 15.758 17.457 15.8191 16.2178Z"
                                        fill="white" />
                                    <circle cx="25.9322" cy="8" r="8" fill="#AE1C9A" />
                                    <path
                                        d="M26.012 13.1392C25.3292 13.1392 24.7194 13.0215 24.1825 12.7862C23.6488 12.5509 23.2263 12.2244 22.9147 11.8068C22.6065 11.3859 22.4407 10.8987 22.4175 10.3452H23.9786C23.9985 10.6468 24.0996 10.9086 24.2819 11.1307C24.4675 11.3494 24.7094 11.5185 25.0077 11.6378C25.306 11.7571 25.6375 11.8168 26.0021 11.8168C26.4031 11.8168 26.7577 11.7472 27.066 11.608C27.3775 11.4687 27.6211 11.2749 27.7968 11.0263C27.9725 10.7744 28.0603 10.4844 28.0603 10.1562C28.0603 9.81487 27.9725 9.51491 27.7968 9.25639C27.6245 8.99455 27.3709 8.78906 27.0361 8.63991C26.7047 8.49077 26.3037 8.41619 25.833 8.41619H24.9729V7.16335H25.833C26.2109 7.16335 26.5423 7.09541 26.8273 6.95952C27.1157 6.82363 27.3411 6.63471 27.5035 6.39276C27.6659 6.14749 27.7471 5.8608 27.7471 5.53267C27.7471 5.2178 27.6758 4.94437 27.5333 4.71236C27.3941 4.47704 27.1952 4.29309 26.9367 4.16051C26.6815 4.02794 26.3799 3.96165 26.0319 3.96165C25.7004 3.96165 25.3906 4.02296 25.1022 4.1456C24.8172 4.26491 24.5852 4.43726 24.4062 4.66264C24.2272 4.88471 24.1311 5.15151 24.1178 5.46307H22.6313C22.6479 4.91288 22.8103 4.42898 23.1185 4.01136C23.4301 3.59375 23.8411 3.26728 24.3515 3.03196C24.8619 2.79664 25.4287 2.67898 26.0518 2.67898C26.7047 2.67898 27.2682 2.80658 27.7421 3.06179C28.2194 3.31368 28.5873 3.65009 28.8458 4.07102C29.1076 4.49195 29.2369 4.95265 29.2336 5.45312C29.2369 6.0232 29.0778 6.5071 28.7563 6.90483C28.4381 7.30256 28.0139 7.56937 27.4836 7.70526V7.7848C28.1597 7.88755 28.6834 8.15601 29.0546 8.5902C29.4291 9.02438 29.6147 9.56297 29.6114 10.206C29.6147 10.7661 29.459 11.2682 29.1441 11.7124C28.8326 12.1565 28.4067 12.5062 27.8664 12.7614C27.3262 13.0133 26.708 13.1392 26.012 13.1392Z"
                                        fill="#F9FFFB" />
                                </svg>
                            </span>
                            <span class="cart-text ">
                                {{ __('site_header.compare') }}
                            </span>
                        </a>
                    </div>
                    @endif
                    @if (Route::has('website.wishlist.index'))
                    @livewire('website.wishlist-header-icon')
                    @endif
                    <div class="header-cart">
                        @livewire('website.cart-header-icon')
                        {{-- The old static cart submenu is disabled until it is connected to real cart data.
                        <div class="cart-submenu">
                            <div class="cart-wrapper-item">
                                <div class="wrapper">
                                    <div class="wrapper-item">
                                        <div class="wrapper-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                                alt="img">
                                        </div>
                                        <div class="wrapper-content">
                                            <h5 class="wrapper-title">{{ __('site_header.classic_design_skart') }}</h5>
                                            <div class="price">
                                                <p class="new-price">$20.00</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="close-btn">
                                        <svg viewBox="0 0 10 10" fill="none" class="fill-current"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.7 0.3C9.3 -0.1 8.7 -0.1 8.3 0.3L5 3.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L3.6 5L0.3 8.3C-0.1 8.7 -0.1 9.3 0.3 9.7C0.7 10.1 1.3 10.1 1.7 9.7L5 6.4L8.3 9.7C8.7 10.1 9.3 10.1 9.7 9.7C10.1 9.3 10.1 8.7 9.7 8.3L6.4 5L9.7 1.7C10.1 1.3 10.1 0.7 9.7 0.3Z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="wrapper">
                                    <div class="wrapper-item">
                                        <div class="wrapper-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/product-img/product-img-2.webp') }}"
                                                alt="img">
                                        </div>
                                        <div class="wrapper-content">
                                            <h5 class="wrapper-title">{{ __('site_header.black_suit') }}</h5>
                                            <div class="price">
                                                <p class="new-price">$10.00</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="close-btn">
                                        <svg viewBox="0 0 10 10" fill="none" class="fill-current"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.7 0.3C9.3 -0.1 8.7 -0.1 8.3 0.3L5 3.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L3.6 5L0.3 8.3C-0.1 8.7 -0.1 9.3 0.3 9.7C0.7 10.1 1.3 10.1 1.7 9.7L5 6.4L8.3 9.7C8.7 10.1 9.3 10.1 9.7 9.7C10.1 9.3 10.1 8.7 9.7 8.3L6.4 5L9.7 1.7C10.1 1.3 10.1 0.7 9.7 0.3Z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="wrapper">
                                    <div class="wrapper-item">
                                        <div class="wrapper-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/product-img/product-img-3.webp') }}"
                                                alt="img">
                                        </div>
                                        <div class="wrapper-content">
                                            <h5 class="wrapper-title">{{ __('site_header.blue_party_dress') }}</h5>
                                            <div class="price">
                                                <p class="new-price">$15.00</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="close-btn">
                                        <svg viewBox="0 0 10 10" fill="none" class="fill-current"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.7 0.3C9.3 -0.1 8.7 -0.1 8.3 0.3L5 3.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L3.6 5L0.3 8.3C-0.1 8.7 -0.1 9.3 0.3 9.7C0.7 10.1 1.3 10.1 1.7 9.7L5 6.4L8.3 9.7C8.7 10.1 9.3 10.1 9.7 9.7C10.1 9.3 10.1 8.7 9.7 8.3L6.4 5L9.7 1.7C10.1 1.3 10.1 0.7 9.7 0.3Z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="wrapper">
                                    <div class="wrapper-item">
                                        <div class="wrapper-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/product-img/product-img-4.webp') }}"
                                                alt="img">
                                        </div>
                                        <div class="wrapper-content">
                                            <h5 class="wrapper-title">{{ __('site_header.classic_red_dress') }}</h5>
                                            <div class="price">
                                                <p class="new-price">$18.00</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="close-btn">
                                        <svg viewBox="0 0 10 10" fill="none" class="fill-current"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.7 0.3C9.3 -0.1 8.7 -0.1 8.3 0.3L5 3.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L3.6 5L0.3 8.3C-0.1 8.7 -0.1 9.3 0.3 9.7C0.7 10.1 1.3 10.1 1.7 9.7L5 6.4L8.3 9.7C8.7 10.1 9.3 10.1 9.7 9.7C10.1 9.3 10.1 8.7 9.7 8.3L6.4 5L9.7 1.7C10.1 1.3 10.1 0.7 9.7 0.3Z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="cart-wrapper-section">
                                <div class="wrapper-line"></div>
                                <div class="wrapper-subtotal">
                                    <h5 class="wrapper-title">{{ __('site_header.subtotal') }}</h5>
                                    <h5 class="wrapper-title">$60</h5>
                                </div>
                                <div class="cart-btn">
                                    <a href="{{ route('website.cart.index') }}" class="shop-btn view-btn">{{ __('site_header.view_cart') }}</a>
                                    @if (Route::has('website.checkout.index'))
                                        <a href="{{ route('website.checkout.index') }}" class="shop-btn checkout-btn">{{ __('site_header.checkout_now') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>
                    <div class="header-user">
                        @auth
                            <form action="{{ route('website.logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="border: 0; background: transparent; padding: 0; cursor: pointer;">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                            height="24" class="fill-current">
                                            <path fill="none" d="M0 0h24v24H0z"></path>
                                            <path
                                                d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H9a2 2 0 0 0-2 2v3h2V5h10v14H9v-3H7v3a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('website.login') }}">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="fill-current">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path
                                            d="M20 22H4v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2zm-8-9a6 6 0 1 1 0-12 6 6 0 0 1 0 12z">
                                        </path>
                                    </svg>
                                </span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="mobile-menu d-block d-lg-none">
        <div class="mobile-menu-header d-flex justify-content-between align-items-center">
            <button class="btn" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <span>
                    <svg width="14" height="9" viewBox="0 0 14 9" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="14" height="1" fill="#1D1D1D" />
                        <rect y="8" width="14" height="1" fill="#1D1D1D" />
                        <rect y="4" width="10" height="1" fill="#1D1D1D" />
                    </svg>
                </span>
            </button>
            <a href="{{ route('website.home') }}" class="mobile-header-logo">
                <img src="{{ asset('website-assets/assets/images/logos/logo.webp') }}" alt="logo">
            </a>
            @livewire('website.cart-header-icon')
        </div>
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions">
            <div class="offcanvas-body">
                <div class="header-top">
                    <div class="header-cart ">
                        @if (Route::has('website.compare.index'))
                        <div class="header-compaire">
                            <a href="{{ route('website.compare.index') }}" class="cart-item">
                                <span>
                                    <svg width="34" height="27" viewBox="0 0 34 27" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22 16.0094C21.997 22.0881 17.0653 27.007 10.9802 27C4.90444 26.9931 -0.00941233 22.0569 1.3538e-05 15.9688C0.00943941 9.89602 4.95157 4.98663 11.0422 5.00003C17.0961 5.01342 22.003 9.94315 22 16.0094ZM6.16553 15.7812C6.40365 12.6236 8.72192 11.2861 10.5868 11.1993C12.3305 11.1179 14.4529 12.3353 14.7465 13.6143C14.2425 13.6143 13.7459 13.6143 13.2429 13.6143C13.2429 14.0241 13.2429 14.3986 13.2429 14.7975C14.308 14.7975 15.3374 14.8064 16.3668 14.793C16.7805 14.7876 17.0102 14.5291 17.0147 14.1005C17.0221 13.3414 17.0172 12.5824 17.0172 11.8234C17.0172 11.558 17.0172 11.2925 17.0172 11.0311C16.5836 11.0311 16.2165 11.0311 15.7908 11.0311C15.7908 11.6046 15.7908 12.1572 15.7908 12.7937C13.9379 10.0444 10.8447 9.4545 8.48578 10.4824C6.21811 11.4706 4.90792 13.847 5.04682 15.7817C5.40997 15.7812 5.77609 15.7812 6.16553 15.7812ZM15.8191 16.2178C15.7581 17.4576 15.3498 18.547 14.4742 19.4286C13.5976 20.3111 12.5265 20.772 11.2858 20.8008C9.57472 20.8405 7.568 19.6424 7.2495 18.3892C7.75403 18.3892 8.25013 18.3892 8.76012 18.3892C8.76012 17.9809 8.76012 17.6064 8.76012 17.2041C7.68458 17.2041 6.64178 17.1921 5.59997 17.21C5.19962 17.2169 5.00069 17.4839 4.99771 17.9442C4.99176 18.803 4.99573 19.6612 4.99573 20.52C4.99573 20.6698 4.99573 20.8196 4.99573 20.964C5.4318 20.964 5.79692 20.964 6.20224 20.964C6.20224 20.3895 6.20224 19.8418 6.20224 19.1686C7.07984 20.4912 8.16976 21.3465 9.58216 21.7617C11.0184 22.1839 12.4114 22.0494 13.7548 21.4035C15.8191 20.4113 17.0946 18.1466 16.9507 16.2178C16.5861 16.2178 16.2209 16.2178 15.8191 16.2178Z"
                                            fill="#6E6D79" />
                                        <path
                                            d="M6.16568 15.7814C5.77624 15.7814 5.41062 15.7814 5.04648 15.7814C4.90757 13.8471 6.21777 11.4703 8.48543 10.482C10.8444 9.45411 13.9376 10.044 15.7905 12.7934C15.7905 12.1569 15.7905 11.6042 15.7905 11.0307C16.2161 11.0307 16.5833 11.0307 17.0168 11.0307C17.0168 11.2917 17.0168 11.5571 17.0168 11.823C17.0168 12.582 17.0218 13.341 17.0144 14.1001C17.0104 14.5287 16.7802 14.7877 16.3665 14.7926C15.3371 14.8055 14.3076 14.7971 13.2425 14.7971C13.2425 14.3982 13.2425 14.0237 13.2425 13.6139C13.7451 13.6139 14.2417 13.6139 14.7462 13.6139C14.4525 12.3355 12.3302 11.118 10.5864 11.1989C8.72207 11.2862 6.4038 12.6237 6.16568 15.7814Z"
                                            fill="white" />
                                        <path
                                            d="M15.8191 16.2178C16.2209 16.2178 16.5865 16.2178 16.9502 16.2178C17.094 18.1466 15.8186 20.4108 13.7543 21.4035C12.4109 22.0494 11.0178 22.1834 9.58161 21.7617C8.16971 21.3469 7.07978 20.4912 6.20169 19.1686C6.20169 19.8418 6.20169 20.3895 6.20169 20.9639C5.79687 20.9639 5.43125 20.9639 4.99518 20.9639C4.99518 20.8201 4.99518 20.6703 4.99518 20.5199C4.99518 19.6612 4.99121 18.8029 4.99716 17.9442C5.00014 17.4838 5.19907 17.2169 5.59943 17.21C6.64173 17.1916 7.68403 17.204 8.75957 17.204C8.75957 17.6064 8.75957 17.9809 8.75957 18.3892C8.25008 18.3892 7.75348 18.3892 7.24895 18.3892C7.56794 19.6428 9.57466 20.8404 11.2852 20.8007C12.526 20.772 13.597 20.3111 14.4736 19.4285C15.3492 18.547 15.758 17.457 15.8191 16.2178Z"
                                            fill="white" />
                                        <circle cx="25.9322" cy="8" r="8" fill="#AE1C9A" />
                                        <path
                                            d="M26.012 13.1392C25.3292 13.1392 24.7194 13.0215 24.1825 12.7862C23.6488 12.5509 23.2263 12.2244 22.9147 11.8068C22.6065 11.3859 22.4407 10.8987 22.4175 10.3452H23.9786C23.9985 10.6468 24.0996 10.9086 24.2819 11.1307C24.4675 11.3494 24.7094 11.5185 25.0077 11.6378C25.306 11.7571 25.6375 11.8168 26.0021 11.8168C26.4031 11.8168 26.7577 11.7472 27.066 11.608C27.3775 11.4687 27.6211 11.2749 27.7968 11.0263C27.9725 10.7744 28.0603 10.4844 28.0603 10.1562C28.0603 9.81487 27.9725 9.51491 27.7968 9.25639C27.6245 8.99455 27.3709 8.78906 27.0361 8.63991C26.7047 8.49077 26.3037 8.41619 25.833 8.41619H24.9729V7.16335H25.833C26.2109 7.16335 26.5423 7.09541 26.8273 6.95952C27.1157 6.82363 27.3411 6.63471 27.5035 6.39276C27.6659 6.14749 27.7471 5.8608 27.7471 5.53267C27.7471 5.2178 27.6758 4.94437 27.5333 4.71236C27.3941 4.47704 27.1952 4.29309 26.9367 4.16051C26.6815 4.02794 26.3799 3.96165 26.0319 3.96165C25.7004 3.96165 25.3906 4.02296 25.1022 4.1456C24.8172 4.26491 24.5852 4.43726 24.4062 4.66264C24.2272 4.88471 24.1311 5.15151 24.1178 5.46307H22.6313C22.6479 4.91288 22.8103 4.42898 23.1185 4.01136C23.4301 3.59375 23.8411 3.26728 24.3515 3.03196C24.8619 2.79664 25.4287 2.67898 26.0518 2.67898C26.7047 2.67898 27.2682 2.80658 27.7421 3.06179C28.2194 3.31368 28.5873 3.65009 28.8458 4.07102C29.1076 4.49195 29.2369 4.95265 29.2336 5.45312C29.2369 6.0232 29.0778 6.5071 28.7563 6.90483C28.4381 7.30256 28.0139 7.56937 27.4836 7.70526V7.7848C28.1597 7.88755 28.6834 8.15601 29.0546 8.5902C29.4291 9.02438 29.6147 9.56297 29.6114 10.206C29.6147 10.7661 29.459 11.2682 29.1441 11.7124C28.8326 12.1565 28.4067 12.5062 27.8664 12.7614C27.3262 13.0133 26.708 13.1392 26.012 13.1392Z"
                                            fill="#F9FFFB" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                        @endif
                        @if (Route::has('website.wishlist.index'))
                        @livewire('website.wishlist-header-icon')
                        @endif
                    </div>
                    <div class="shop-btn">
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                        </button>
                    </div>
                    <div class="header-profile">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                <span>{{ strtoupper($localeCode) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="header-input">
                    <input type="text" placeholder="{{ __('site_header.search') }}">
                    <span>
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.9708 16.4151C12.5227 17.4021 10.9758 17.9723 9.27353 18.0062C5.58462 18.0802 2.75802 16.483 1.05056 13.1945C-1.76315 7.77253 1.33485 1.37571 7.25086 0.167548C12.2281 -0.848249 17.2053 2.87895 17.7198 7.98579C17.9182 9.95558 17.5566 11.7939 16.5852 13.5061C16.4512 13.742 16.483 13.8725 16.6651 14.0553C18.2412 15.6386 19.8112 17.2272 21.3735 18.8244C22.1826 19.6513 22.2058 20.7559 21.456 21.4932C20.7697 22.1678 19.7047 22.1747 18.9764 21.4793C18.3623 20.8917 17.7774 20.2737 17.1796 19.6688C16.118 18.5929 15.0564 17.5153 13.9708 16.4151ZM2.89545 9.0364C2.91692 12.4172 5.59664 15.1164 8.91967 15.1042C12.2384 15.092 14.9138 12.3493 14.8889 8.98505C14.864 5.63213 12.1826 2.92508 8.89047 2.92857C5.58204 2.93118 2.87397 5.68958 2.89545 9.0364Z"
                                fill="black"></path>
                        </svg>
                    </span>
                </div>

                @auth
                    <form action="{{ route('website.logout') }}" method="POST" style="margin-bottom: 15px;">
                        @csrf
                        <button type="submit" class="shop-btn" style="border: 0; width: 100%;">
                            {{ __('site_header.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('website.login') }}" class="shop-btn" style="display: block; margin-bottom: 15px; text-align: center;">
                        {{ __('site_header.account') }}
                    </a>
                @endauth
                <div class="category-dropdown">
                    <ul class="category-list">
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/dresses.webp') }}"
                                                alt="dress">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.dresses') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/bags.webp') }}"
                                                alt="{{ __('site_header.bags') }}">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.bags') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/sweaters.webp') }}"
                                                alt="sweaters">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.sweaters') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/shoes.webp') }}"
                                                alt="shoes">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.boots') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/gift.webp') }}"
                                                alt="gift">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.gifts') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/sneakers.webp') }}"
                                                alt="sneakers">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.sneakers') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/watch.webp') }}"
                                                alt="watch">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.watches') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/ring.webp') }}"
                                                alt="ring">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.gold_ring') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/cap.webp') }}"
                                                alt="cap">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.cap') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/glass.webp') }}"
                                                alt="glass">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.sunglasses') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="category-list-item">
                            <a href="{{ route('website.categories.index') }}">
                                <div class="dropdown-item d-flex justify-content-between align-items-center">
                                    <div class="dropdown-list-item d-flex">
                                        <span class="dropdown-img">
                                            <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/baby.webp') }}"
                                                alt="baby">
                                        </span>
                                        <span class="dropdown-text">
                                            {{ __('site_header.baby_shop') }}
                                        </span>
                                    </div>
                                    <div class="drop-down-list-icon">
                                        <span>
                                            <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                    transform="rotate(45 1.5 0.818359)" />
                                                <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                    transform="rotate(135 5.58984 4.90918)" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="header-bottom d-lg-block d-none">
        <div class="container">
            <div class="header-nav">
                <div class="category-menu-section position-relative">
                    <div class="empty position-fixed" onclick="tooglmenu()"></div>
                    <button class="dropdown-btn" onclick="tooglmenu()">
                        <span class="dropdown-icon">
                            <svg width="14" height="9" viewBox="0 0 14 9" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="14" height="1" />
                                <rect y="8" width="14" height="1" />
                                <rect y="4" width="10" height="1" />
                            </svg>
                        </span>
                        <span class="list-text">
                            {{ __('site_header.all_categories') }}
                        </span>
                    </button>
                    <div class="category-dropdown position-absolute" id="subMenu">
                        <ul class="category-list">
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item">
                                        <div class="dropdown-list-item">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/dresses.webp') }}"
                                                    alt="dress">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.dresses') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" fill="#1D1D1D" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" fill="#1D1D1D" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/bags.webp') }}"
                                                    alt="{{ __('site_header.bags') }}">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.bags') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/sweaters.webp') }}"
                                                    alt="sweaters">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.sweaters') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/shoes.webp') }}"
                                                    alt="sweaters">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.boots') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/gift.webp') }}"
                                                    alt="gift">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.gifts') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/sneakers.webp') }}"
                                                    alt="sneakers">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.sneakers') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" fill="#1D1D1D" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" fill="#1D1D1D" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/watch.webp') }}"
                                                    alt="watch">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.watches') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/ring.webp') }}"
                                                    alt="ring">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.gold_ring') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/cap.webp') }}"
                                                    alt="cap">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.cap') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/glass.webp') }}"
                                                    alt="glass">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.sunglasses') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="category-list-item">
                                <a href="{{ route('website.categories.index') }}">
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="dropdown-list-item d-flex">
                                            <span class="dropdown-img">
                                                <img src="{{ asset('website-assets/assets/images/homepage-one/category-img/baby.webp') }}"
                                                    alt="baby">
                                            </span>
                                            <span class="dropdown-text">
                                                {{ __('site_header.baby_shop') }}
                                            </span>
                                        </div>
                                        <div class="drop-down-list-icon">
                                            <span>
                                                <svg width="6" height="9" viewBox="0 0 6 9" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="0.818359" width="5.78538" height="1.28564"
                                                        transform="rotate(45 1.5 0.818359)" />
                                                    <rect x="5.58984" y="4.90918" width="5.78538" height="1.28564"
                                                        transform="rotate(135 5.58984 4.90918)" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="header-nav-menu">
                    <ul class="menu-list">
                        <li>
                            <a href="{{ route('website.home') }}">
                                <span class="list-text">{{ __('site_header.home') }}</span>
                            </a>
                        </li>
                        <li class="mega-menu">
                            <a href="{{ route('website.categories.index') }}">
                                <span class="list-text">{{ __('site_header.shop') }}</span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_1_183)">
                                            <path
                                                d="M2.37811 5.89491C1.88356 5.89491 1.38862 5.90351 0.894066 5.89218C0.443267 5.88202 0.108098 5.59451 0.0178597 5.17027C-0.0641747 4.7851 0.137786 4.36204 0.508895 4.20305C0.659291 4.13859 0.83586 4.11008 1.00071 4.10851C1.93786 4.09992 2.87539 4.10461 3.81254 4.10422C4.07075 4.10422 4.10357 4.07062 4.10396 3.80889C4.10474 2.85847 4.102 1.90843 4.10513 0.958001C4.10669 0.513061 4.336 0.177111 4.71218 0.0501527C5.30752 -0.151027 5.88567 0.278287 5.89387 0.937687C5.90168 1.56232 5.89582 2.18735 5.89582 2.81237C5.89582 3.14441 5.89504 3.47646 5.89621 3.80811C5.897 4.07023 5.92942 4.10422 6.18685 4.10422C7.13728 4.105 8.08732 4.10265 9.03774 4.10539C9.48503 4.10656 9.81941 4.33235 9.94872 4.70776C10.1534 5.30192 9.72605 5.88437 9.06782 5.89413C8.50803 5.90233 7.94825 5.89608 7.38846 5.89608C6.97829 5.89608 6.56851 5.89491 6.15833 5.89687C5.93918 5.89804 5.897 5.94023 5.8966 6.1625C5.89543 7.11918 5.89778 8.07625 5.89543 9.03293C5.89426 9.48216 5.67238 9.81577 5.29736 9.94741C4.70437 10.1552 4.11841 9.72983 4.10669 9.07316C4.09771 8.57861 4.10474 8.08367 4.10474 7.58912C4.10474 7.12035 4.10552 6.65197 4.10435 6.1832C4.10396 5.93398 4.06841 5.89726 3.82387 5.89687C3.34221 5.89569 2.86055 5.89647 2.37889 5.89647C2.37811 5.8953 2.37811 5.8953 2.37811 5.89491Z"
                                                fill="white" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1_185">
                                                <rect width="10" height="10" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </span>
                            </a>
                            <div class="shop-menu">
                                <div class="menu-wrapper">
                                    <div class="menu-list">
                                        <h5 class="menu-title">{{ __('site_header.dresses') }}</h5>
                                        <ul>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.shirt') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.skart') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}"> {{ __('site_header.t_shirt') }}</a></li>
                                        </ul>
                                    </div>
                                    <div class="menu-list">
                                        <h5 class="menu-title">{{ __('site_header.bags') }}</h5>
                                        <ul>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.handbags') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.mobile_bags') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.school_bags') }}</a></li>
                                        </ul>
                                    </div>
                                    <div class="menu-list">
                                        <h5 class="menu-title">{{ __('site_header.cosmetics') }}</h5>
                                        <ul>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.liptics') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}">{{ __('site_header.foundation') }}</a></li>
                                            <li><a href="{{ route('website.categories.index') }}"> {{ __('site_header.eye_liner') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="shop-menu-img">
                                    <img src="{{ asset('website-assets/assets/images/homepage-one/empty-wishlist.webp') }}"
                                        alt="img">
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#">
                                <span class="list-text">{{ __('site_header.pages') }}</span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_1_183)">
                                            <path
                                                d="M2.37811 5.89491C1.88356 5.89491 1.38862 5.90351 0.894066 5.89218C0.443267 5.88202 0.108098 5.59451 0.0178597 5.17027C-0.0641747 4.7851 0.137786 4.36204 0.508895 4.20305C0.659291 4.13859 0.83586 4.11008 1.00071 4.10851C1.93786 4.09992 2.87539 4.10461 3.81254 4.10422C4.07075 4.10422 4.10357 4.07062 4.10396 3.80889C4.10474 2.85847 4.102 1.90843 4.10513 0.958001C4.10669 0.513061 4.336 0.177111 4.71218 0.0501527C5.30752 -0.151027 5.88567 0.278287 5.89387 0.937687C5.90168 1.56232 5.89582 2.18735 5.89582 2.81237C5.89582 3.14441 5.89504 3.47646 5.89621 3.80811C5.897 4.07023 5.92942 4.10422 6.18685 4.10422C7.13728 4.105 8.08732 4.10265 9.03774 4.10539C9.48503 4.10656 9.81941 4.33235 9.94872 4.70776C10.1534 5.30192 9.72605 5.88437 9.06782 5.89413C8.50803 5.90233 7.94825 5.89608 7.38846 5.89608C6.97829 5.89608 6.56851 5.89491 6.15833 5.89687C5.93918 5.89804 5.897 5.94023 5.8966 6.1625C5.89543 7.11918 5.89778 8.07625 5.89543 9.03293C5.89426 9.48216 5.67238 9.81577 5.29736 9.94741C4.70437 10.1552 4.11841 9.72983 4.10669 9.07316C4.09771 8.57861 4.10474 8.08367 4.10474 7.58912C4.10474 7.12035 4.10552 6.65197 4.10435 6.1832C4.10396 5.93398 4.06841 5.89726 3.82387 5.89687C3.34221 5.89569 2.86055 5.89647 2.37889 5.89647C2.37811 5.8953 2.37811 5.8953 2.37811 5.89491Z"
                                                fill="white" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1_18">
                                                <rect width="10" height="10" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </span>
                            </a>
                            <ul class="header-sub-menu">
                                @foreach ($dynamicPages as $dynamicPage)
                                    <li>
                                        <a href="{{ route('website.pages.show', $dynamicPage->slug) }}">
                                            {{ $dynamicPage->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('website.faqs.index') }}">
                                <span class="list-text">{{ __('site_header.faq') }}</span>
                            </a>
                        </li>
                        @if (Route::has('website.about'))
                        <li>
                            <a href="{{ route('website.about') }}">
                                <span class="list-text">{{ __('site_header.about') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (Route::has('website.blogs.index'))
                        <li>
                            <a href="{{ route('website.blogs.index') }}">
                                <span class="list-text">{{ __('site_header.blog') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (Route::has('website.profile'))
                        <li>
                            <a href="{{ route('website.profile') }}">
                                <span class="list-text">{{ __('site_header.user_dashboard') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (Route::has('website.contact'))
                        <li>
                            <a href="{{ route('website.contact') }}">
                                <span class="list-text">{{ __('site_header.contact') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                @if (Route::has('website.vendors.create'))
                <div class="header-vendor-btn">
                    <a href="{{ route('website.vendors.create') }}" class="shop-btn">
                        <span class="list-text shop-text">{{ __('site_header.become_vendor') }}</span>
                        <span class="icon">
                            <svg width="24" height="16" viewBox="0 0 24 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.257 7.07205C20.038 7.07205 19.8474 7.07205 19.6563 7.07205C17.4825 7.07205 15.3086 7.07205 13.1352 7.07205C10.1545 7.07205 7.17336 7.0725 4.19265 7.0725C3.30392 7.0725 2.41519 7.07024 1.52646 7.07295C1.12124 7.07431 0.809811 7.25265 0.625785 7.62651C0.43866 8.00623 0.488204 8.37556 0.737704 8.70426C0.932347 8.96027 1.20529 9.08173 1.52867 9.08037C2.20948 9.07766 2.8903 9.07902 3.57111 9.07902C5.95285 9.07902 8.33415 9.07902 10.7159 9.07902C13.782 9.07902 16.8485 9.07902 19.9146 9.07902C20.0274 9.07902 20.1398 9.07902 20.2822 9.07902C20.1871 9.18332 20.1141 9.26865 20.0358 9.34857C19.5656 9.82672 19.0922 10.3022 18.6229 10.7812C18.1363 11.2779 17.6541 11.7791 17.1675 12.2757C16.4942 12.9634 15.8116 13.6415 15.1476 14.3391C14.9096 14.5893 14.8455 14.9157 14.9406 15.2575C15.156 16.0305 16.0567 16.2499 16.6119 15.6769C17.4342 14.8286 18.2655 13.9892 19.0927 13.1458C19.6948 12.5317 20.2968 11.9172 20.8985 11.3023C21.5952 10.5902 22.2911 9.87729 22.9878 9.1648C23.1059 9.04425 23.2249 8.9246 23.3435 8.8045C23.6903 8.45367 23.7239 7.84278 23.3943 7.4766C22.998 7.03683 22.5852 6.61241 22.1756 6.18573C21.7965 5.79066 21.4134 5.39965 21.0303 5.00909C20.6733 4.64473 20.3132 4.28306 19.9553 3.91915C19.6147 3.57284 19.2754 3.22563 18.9356 2.87887C18.5154 2.44948 18.0951 2.01964 17.6744 1.5907C17.2511 1.15861 16.8198 0.734188 16.4057 0.29261C16.0363 -0.101559 15.3697 -0.0816927 15.0344 0.257392C14.6238 0.672782 14.5999 1.26381 14.995 1.68552C15.3378 2.0517 15.6957 2.40342 16.0465 2.76192C16.929 3.66449 17.8111 4.56797 18.6937 5.47054C19.1829 5.97081 19.6735 6.47018 20.1632 6.97046C20.1885 6.99574 20.2123 7.02329 20.257 7.07205Z" />
                            </svg>
                        </span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</header>
