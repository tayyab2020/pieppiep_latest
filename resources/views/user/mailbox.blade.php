@extends('layouts.handyman')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Mailbox types -->
        <div class="col-md-3">
            <h4>Mailbox</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item active">Inbox</li>
                <li class="list-group-item"><a href="#">Sent</a></li>
                <li class="list-group-item"><a href="#">Drafts</a></li>
                <li class="list-group-item"><a href="#">Trash</a></li>
            </ul>
            <a href="{{ route('send-email') }}" class="btn btn-primary btn-block">Compose Email</a>
        </div>

        <!-- Email List -->
        <div class="col-md-3">
            <div class="email-list">
                <ul class="list-group">
                    @foreach ($emails as $index => $email)
                        <li class="list-group-item email-summary" data-target="#emailBody{{ $index }}">
                            <h5 class="email-subject" data-target="#emailBody{{ $index }}">{{ $email['subject'] }}</h5>
                            <p><strong>From:</strong> {{ $email['from'] }}</p>
                            <p><strong>Date:</strong> {{ $email['date'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Email Content -->
        <div class="col-md-6">
            <h2>{{ $folderName }}</h2>
            @foreach ($emails as $index => $email)
                <div id="emailBody{{ $index }}" class="email-body d-none">
                    {!! $email['message'] !!}
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.email-subject').forEach(item => {
        item.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            document.querySelectorAll('.email-body').forEach(body => {
                if ('#' + body.id === targetId) {
                    body.classList.toggle('d-none');
                } else {
                    body.classList.add('d-none');
                }
            });
        });
    });
});
</script>
@endpush

<style>
    .email-list {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #ddd;
    }
    .email-body {
        background-color: #f9f9f9;
        padding: 10px;
        border-radius: 5px;
        white-space: pre-wrap;
    }
    .d-none {
        display: none;
    }
</style>
@endsection
