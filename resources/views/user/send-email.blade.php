@extends('layouts.handyman')

@section('content')
    <div style="padding-top: 50px;" class="container">
        <h2>Send Email</h2>
        <form action="{{ route('send-email') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="to">To:</label>
                <input type="email" name="to" id="to" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" name="subject" id="subject" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="attachments">Attachments:</label>
                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Send Email</button>
        </form>
    </div>
@endsection
