@if ($use_vite == false)
<style>
    {!!$css!!}
</style>
@else
@vite(['resources/css/app.css'])
@endif