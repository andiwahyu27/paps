@props(['judul', 'href' => 'javascript:void(0);'])

<a
    {{ $attributes->merge(['class' => 'btn btn-danger btn-sm rounded-pill']) }}
    href="{{ $href }}"
    data-bs-toggle="tooltip"
    data-bs-offset="0,4"
    data-bs-placement="top"
    data-bs-html="true"
    title="{{ $judul }}"
>
    <i class="bx bx-trash"></i>
</a>

