@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Reset Password</div>
            <div class="card-body">
                @if(session('otp'))
                <div class="alert alert-info">
                    Your OTP is: <strong>{{ session('otp') }}</strong> (for demo purposes)
                </div>
                @endif
                <form method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                    <div class="mb-3">
                        <label for="otp" class="form-label">OTP</label>
                        <input type="text" class="form-control @error('otp') is-invalid @enderror" id="otp" name="otp" required>
                        @error('otp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection