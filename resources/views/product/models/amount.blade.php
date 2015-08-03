@if(isset($amount))
<span class="amount">
    @amount($amount)
</span>
@endif
@if(isset($currency))
    @include('product.models.currency',['currency'=>$currency])
@endif
