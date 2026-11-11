<span class="badge bg-{{ $status === 'submitted' ? 'success' : 'warning' }}">{{ match ($status) {
    'submitted' => 'Terkirim',
    'revision_draft' => 'Draf Revisi',
    default => 'Draf',
} }}</span>
