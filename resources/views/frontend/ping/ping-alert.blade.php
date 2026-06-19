<style>
    .ping-dropdown{
        max-height: 180px;
        overflow-y: auto;
    }
</style>
@if(Auth::user()->role == "Staff")

    @if($ticket->is_forwarded && $ticket->ticket_by != Auth::id())

        <div class="dropdown-menu ping-dropdown">

            <div class="dropdown-title">Ping!</div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>

            @if(\App\Models\User::find($ticket->ticket_by)->role != 'HOD' && \App\Models\User::find($ticket->ticket_by)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_by]) }}">{{ \App\Models\User::find($ticket->ticket_by)->name }} - {{ \App\Models\User::find($ticket->ticket_by)->department }}</a>

            @endif

            <div class="dropdown-divider"></div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>

            @if($ticket->assigned_to != Null && \App\Models\User::find($ticket->assigned_to)->role != 'HOD' && \App\Models\User::find($ticket->assigned_to)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->assigned_to]) }}">{{ \App\Models\User::find($ticket->assigned_to)->name }} - {{ \App\Models\User::find($ticket->assigned_to)->department }}</a>

            @endif

            @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                    @if($forward->assigned_to != Null)

                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                    
                    @endif

                @endforeach

            @endif
                
        </div>        

    @endif

    @if(Auth::id() == $ticket->ticket_by)

        <div class="dropdown-menu ping-dropdown">

            <div class="dropdown-title">Ping!</div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>

            @if($ticket->assigned_to != Null && \App\Models\User::find($ticket->assigned_to)->role != 'HOD' && \App\Models\User::find($ticket->assigned_to)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->assigned_to]) }}">{{ \App\Models\User::find($ticket->assigned_to)->name }} - {{ \App\Models\User::find($ticket->assigned_to)->department }}</a>

            @endif

            @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                    @if($forward->assigned_to != Null)

                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                    
                    @endif

                @endforeach

            @endif
                
        </div>

    @elseif($ticket->assigned_to != Null && Auth::id() == $ticket->assigned_to)

        <div class="dropdown-menu ping-dropdown">

            <div class="dropdown-title">Ping!</div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>

            @if(\App\Models\User::find($ticket->ticket_by)->role != 'HOD' && \App\Models\User::find($ticket->ticket_by)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_by]) }}">{{ \App\Models\User::find($ticket->ticket_by)->name }} - {{ \App\Models\User::find($ticket->ticket_by)->department }}</a>

            @endif

            @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                    @if($forward->assigned_to != Null)

                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                    
                    @endif

                @endforeach

            @endif
                
        </div>

    @endif


@else
    
    @if(Auth::user()->department == $ticket->ticket_to)

    <div class="dropdown-menu ping-dropdown">

        <div class="dropdown-title">Ping!</div>

        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>

        @php
            $user = \App\Models\User::find($ticket->ticket_by);
        @endphp
        
        @if($user && $user->role != 'HOD' && $user->role != 'SuperAdmin')
            <a class="dropdown-item"
               href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $user->id]) }}">
                {{ $user->name }} - {{ $user->department }}
            </a>
        @endif

        @if($ticket->assigned_to != Null && $ticket->assigned_to != Auth::id())
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->assigned_to]) }}">{{ \App\Models\User::find($ticket->assigned_to)->name }} - {{ \App\Models\User::find($ticket->assigned_to)->department }}</a>
        @endif

        @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                        @if($forward->assigned_to != Null)

                            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                        
                        @endif

                @endforeach

            @endif
            
    </div>

    @elseif(Auth::user()->department == $ticket->ticket_from)

    <div class="dropdown-menu ping-dropdown">

        <div class="dropdown-title">Ping!</div>

        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>

        @if($ticket->assigned_to != Null && \App\Models\User::find($ticket->assigned_to)->role != 'HOD' && \App\Models\User::find($ticket->assigned_to)->role != 'SuperAdmin')

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->assigned_to]) }}">{{ \App\Models\User::find($ticket->assigned_to)->name }} - {{ \App\Models\User::find($ticket->assigned_to)->department }}</a>

        @endif

        @if($ticket->ticket_by != Auth::id())

        <div class="dropdown-divider"></div>

        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_by]) }}">{{ \App\Models\User::find($ticket->ticket_by)->name }} - {{ \App\Models\User::find($ticket->ticket_by)->department }}</a>

        @endif

        @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                        @if($forward->assigned_to != Null)

                            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                        
                        @endif
                    
                @endforeach

            @endif
            
    </div>

    @elseif($ticket->is_forwarded)

        <div class="dropdown-menu ping-dropdown">

            <div class="dropdown-title">Ping!</div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>

            @if(\App\Models\User::find($ticket->ticket_by)->role != 'HOD' && \App\Models\User::find($ticket->ticket_by)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_by]) }}">{{ \App\Models\User::find($ticket->ticket_by)->name }} - {{ \App\Models\User::find($ticket->ticket_by)->department }}</a>

            @endif

            <div class="dropdown-divider"></div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>

            @if($ticket->assigned_to != Null && \App\Models\User::find($ticket->assigned_to)->role != 'HOD' && \App\Models\User::find($ticket->assigned_to)->role != 'SuperAdmin')

                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->assigned_to]) }}">{{ \App\Models\User::find($ticket->assigned_to)->name }} - {{ \App\Models\User::find($ticket->assigned_to)->department }}</a>

            @endif

            @if($ticket->is_forwarded)

                @php
                    $forwards = App\Models\TicketForwarding::where('ticket_id', $ticket->id)->get();
                @endphp

                @foreach($forwards as $forward)

                    @if(Auth::user()->department == $forward->forwarded_to)

                        <div class="dropdown-divider"></div>
                        @if($forward->assigned_to != Null)

                            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                        
                        @endif
                    @else
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $forward->forwarded_to }} - Head</a>

                        @if($forward->assigned_to != Null)

                            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $forward->assigned_to]) }}">{{ \App\Models\User::find($forward->assigned_to)->name }} - {{ \App\Models\User::find($forward->assigned_to)->department }}</a>
                        
                        @endif
                    @endif

                @endforeach

            @endif
                
        </div>        

    @endif
    
    @if(Auth::user()->role == 'SuperAdmin' && (Auth::user()->department != $ticket->ticket_by || Auth::user()->department != $ticket->ticket_to))
    
        <div class="dropdown-menu ping-dropdown">

            <div class="dropdown-title">Ping!</div>

            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>
            
            <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>
                
        </div>
    
    @endif

@endif

<script>
    $(document).ready(function() {
        $(".ping-dropdown").niceScroll({
            cursoropacitymin: .3,
            cursoropacitymax: .8,
            cursorwidth: 7
        });
    });
</script>