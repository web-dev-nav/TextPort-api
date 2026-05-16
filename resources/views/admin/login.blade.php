<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TextPort Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .wrap { min-height: 100vh; display: grid; place-items: center; padding: 16px; }
        .card { width: 100%; max-width: 420px; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        h1 { margin: 0 0 16px; font-size: 24px; }
        label { display: block; margin: 12px 0 6px; font-weight: 600; }
        input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; margin-top: 16px; padding: 11px; border: 0; border-radius: 8px; background: #111827; color: #fff; font-weight: 600; cursor: pointer; }
        .err { color: #b91c1c; margin: 8px 0 0; font-size: 14px; }
    </style>
</head>
<body>
<div class="wrap">
    <form class="card" method="post" action="{{ route('admin.login.submit') }}">
        @csrf
        <h1>Admin Login</h1>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        @error('email')<p class="err">{{ $message }}</p>@enderror
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        @error('password')<p class="err">{{ $message }}</p>@enderror
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
