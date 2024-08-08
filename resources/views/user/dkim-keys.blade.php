<!-- resources/views/user/dkim-keys.blade.php -->
@extends('layouts.handyman')


@section('content')
<div style="padding-top: 50px;" class="container">
    <h2>DKIM Keys</h2>
    <div class="form-group">
        <label for="selector">Selector</label>
        <input type="text" name="selector" id="selector" class="form-control" value="{{ $selector }}" readonly>
    </div>
    <div class="form-group">
        <label for="domain">Domain</label>
        <input type="text" name="domain" id="domain" class="form-control" value="{{ $domain }}" readonly>
    </div>
    <div class="form-group">
        <label for="publicKey">Public Key</label>
        <textarea name="publicKey" id="publicKey" class="form-control" rows="10" readonly>{{ $publicKey }}</textarea>
    </div>
</div>
@endsection
