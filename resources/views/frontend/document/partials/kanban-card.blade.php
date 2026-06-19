<!-- Create this partial file: resources/views/frontend/document/partials/kanban-card.blade.php -->
@php
    $role = \App\Models\User::FindorFail($log->by)->department;
    if($role == 'Pro-VC'){
        $style = 'provc';
    }elseif($role == 'VC'){
        $style = 'vc';
    }elseif($role == 'Registrar'){
        $style = 'registrar';
    }else{
        $style = '';
    }
@endphp

<div class="kanban-card {{ $type }}-card {{ $style }}">
    <div class="card-header">
        <span class="card-status">{{ $log->status }}</span>
        <span class="card-role">{{ $role }}</span>
    </div>
    <div class="card-date">
        {{ date('d/m/Y h:i A', strtotime($log->created_at)) }}
    </div>
    <div class="card-message">
        {!! $log->message !!}
    </div>
</div>