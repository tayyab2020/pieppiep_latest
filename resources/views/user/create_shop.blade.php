@extends('layouts.handyman')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($organization->shop_name) ? 'Edit Shop' : 'Register Shop' }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

@section('content')
    <div class="container mt-5">
        <h1>{{ isset($organization->shop_name) ? 'Edit Shop' : 'Register Shop' }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('shop.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="shop_name">Shop Name</label>
                <input type="text" class="form-control" id="shop_name" name="shop_name" value="{{ old('shop_name', $organization->shop_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label for="subdomain">Subdomain (optional)</label>
                <input type="text" class="form-control" id="subdomain" name="subdomain" value="{{ old('subdomain', $organization->subdomain ?? '') }}">
            </div>
            <div class="form-group">
                <label for="shop_description">Shop Description (optional)</label>
                <textarea class="form-control" id="shop_description" name="shop_description">{{ old('shop_description', $organization->shop_description ?? '') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">{{ isset($organization->shop_name) ? 'Update Shop' : 'Register Shop' }}</button>
        </form>


    </div>
@endsection
