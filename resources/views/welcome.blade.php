<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Application</title>
</head>
<body>
    <h1>Welcome, {{ $name }}!</h1>
    <p>Thank you for registering with our application.</p>
    <p>Please verify your email address by clicking the link below:</p>
    <a href="{{ $verificationUrl }}">Verify Email Address</a>
    <p>If you did not create an account, no further action is required.</p>
</body>
</html>