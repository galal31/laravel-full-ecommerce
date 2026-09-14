@if ($product->hasActiveDiscount())
    @php
        $remainingDays = $product->getDiscountDaysRemaining();
        $endDate = $product->end_discount
            ? \Illuminate\Support\Carbon::parse($product->end_discount)->format('Y-m-d')
            : null;
    @endphp

    <p class="{{ $className }}">
        @if ($remainingDays === 0)
            {{ __('website.discount_ends_today', ['date' => $endDate]) }}
        @elseif ($remainingDays !== null)
            {{ __('website.discount_ends_in', [
                'days' => $remainingDays,
                'date' => $endDate,
            ]) }}
        @else
            {{ __('website.discount_without_end_date') }}
        @endif
    </p>
@endif
