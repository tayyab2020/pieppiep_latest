<!-- resources/views/user/generate-dkim.blade.php -->
@extends('layouts.handyman')

@section('content')
<div style="padding-top: 50px;" class="container">
    <h1>Generate DKIM Keys</h1>
    <form action="{{ route('post-generate-dkim') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="domain">Domain:</label>
            <input type="text" id="domain" name="domain" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Generate DKIM Keys</button>
    </form>
</div>
@endsection
