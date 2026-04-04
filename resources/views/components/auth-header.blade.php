@props(['title', 'description'])

<div class="flex w-full flex-col text-center gap-2">
    <h1 class="text-3xl font-bold tracking-tight text-white">
        {{ $title }}
    </h1>
    <p class="text-neutral-400 text-sm">
        {{ $description }}
    </p>
</div>
