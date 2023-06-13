<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    <section class="container mx-auto">
        <div class="mt-4">
            <h1 class="text-3xl">Products</h1>
            <div class="flex gap-8 mt-4">
                @foreach ($products as $product)
                <div class="flex-1 space-y-4">
                    <img src="{{ $product->image }}" alt="" style="max-width: 100%">
                    <h5>{{ $product->name }}</h5>
                    <p>{{ __('Total:') }} @money($product->price, 'INR')</p>
                </div>
                @endforeach
            </div>
            <p>
            <form action="{{ route('checkout') }}" method="post">
                @csrf
                <button class="primary border-2 px-4 py-1 border-blue-500 rounded mt-4">Checkout</button>
            </form>
            </p>
        </div>
    </section>
</body>

</html>