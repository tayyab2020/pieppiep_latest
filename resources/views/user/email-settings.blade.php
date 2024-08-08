@extends('layouts.handyman')

@section('content')
<div style="padding-top: 50px;" class="container">
    <h1>Email Settings</h1>
    <form action="{{ route('email-settings.save') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="host">SMTP Host</label>
            <input type="text" class="form-control" id="host" name="host" value="{{ old('host', $emailSettings->host ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="port">SMTP Port</label>
            <input type="text" class="form-control" id="port" name="port" value="{{ old('port', $emailSettings->port ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="imap_port">IMAP Port</label>
            <input type="text" class="form-control" id="imap_port" name="imap_port" value="{{ old('imap_port', $emailSettings->imap_port ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="encryption">Encryption</label>
            <input type="text" class="form-control" id="encryption" name="encryption" value="{{ old('encryption', $emailSettings->encryption ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $emailSettings->username ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="{{ old('password', $emailSettings->password ?? '') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
