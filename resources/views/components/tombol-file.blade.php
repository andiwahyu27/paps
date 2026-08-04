@props(['path', 'label' => 'Download File'])

@if ($path)
    <a href="{{ asset($path) }}" class="btn btn-sm rounded-pill btn-primary" target="_blank"
        data-bs-toggle="tooltip"
        data-bs-offset="0,4" data-bs-placement="bottom" data-bs-html="true"
        data-bs-original-title="<span>download file</span>"
        >
        <i class="bx bx-xs bxs-download"></i> {{ $label }}
    </a>
@else
    <a href="#" class="btn btn-sm rounded-pill btn-secondary"
        data-bs-toggle="tooltip" data-bs-offset="0,4"
        data-bs-placement="bottom" data-bs-html="true" data-bs-original-title="<span>file tidak ada</span>"
        >
        <i class="bx bx-xs bxs-error"></i> {{ $label }}
    </a>
@endif
