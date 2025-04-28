@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Forgot Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.otp.send') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send OTP</button>
                </form>
                <div class="mt-3">
                    Remember your password? <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection