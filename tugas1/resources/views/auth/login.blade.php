<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f3f4f6;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .card {
                background: white;
                width: 420px;
                padding: 30px 28px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            }
            h1 {
                margin: 0 0 8px;
                font-size: 2rem;
            }
            p {
                margin: 0 0 20px;
                color: #4b5563;
            }
            .error {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fecaca;
                border-radius: 8px;
                padding: 10px 12px;
                margin-bottom: 16px;
            }
            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
            }
            input {
                width: 100%;
                box-sizing: border-box;
                padding: 10px 12px;
                margin-bottom: 16px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 1rem;
            }
            button {
                width: 100%;
                background: #2563eb;
                border: none;
                color: white;
                padding: 12px;
                border-radius: 8px;
                font-size: 1rem;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Login</h1>
            <p>Masuk untuk membuka dashboard</p>

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first('login') ?: 'Username atau password salah.' }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required>
                </div>

                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                </div>

                <button type="submit">Masuk</button>
            </form>
        </div>
    </body>
</html>
