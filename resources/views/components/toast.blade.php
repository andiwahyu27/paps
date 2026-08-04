@php
    $type = $type ?? 'success';
    $message = $message ?? '';
@endphp

<div
    id="myToast"
    class="bs-toast toast toast-placement-ex m-2 fade hide bg-{{ $type === 'error' ? 'danger' : 'success' }} top-10 start-50 translate-middle-x"
    role="alert"
    aria-live="assertive"
    aria-atomic="true"
    data-bs-autohide="true"
>
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">
            {{ ucfirst($type) }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        @if (is_array($message) && count($message) > 1)
            <ul class="mb-0">
                @foreach ($message as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        @elseif (is_array($message))
            {{ $message[0] ?? '' }}
        @else
            {{ $message }}
        @endif
    </div>
</div>
