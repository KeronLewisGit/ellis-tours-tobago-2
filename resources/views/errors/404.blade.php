<x-layout title="Page not found">
    <section class="section">
        <div class="wrap max-w-2xl text-center">
            <x-icon name="compass" class="mx-auto size-12 text-brand-500" />
            <p class="eyebrow mt-6 justify-center">Error 404</p>
            <h1 class="h-display mt-4">We have wandered off the trail.</h1>
            <p class="lead mt-4">The page you are looking for has moved or does not exist. Ellis knows the way back.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to the home page</a>
                <a href="{{ route('tours') }}" class="btn btn-light"><x-icon name="map" class="size-4" /> See the tours</a>
            </div>
        </div>
    </section>
</x-layout>
