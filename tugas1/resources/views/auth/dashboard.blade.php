<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f3f4f6;
                margin: 0;
                padding: 24px;
            }
            .container {
                max-width: 760px;
                margin: 40px auto;
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                padding: 24px;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 16px;
                margin-bottom: 20px;
            }
            .header h1 {
                margin: 0;
                font-size: 2rem;
            }
            form {
                margin: 0;
            }
            button {
                background: #dc2626;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 10px 16px;
                font-size: 0.95rem;
                cursor: pointer;
            }
            .welcome {
                font-size: 1.25rem;
                margin-bottom: 12px;
            }
            .meta {
                color: #4b5563;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Dashboard</h1>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>

            <div class="welcome">Selamat datang, {{ $user->nama_lengkap }}!</div>
            <div class="meta">Halaman ini hanya bisa dibuka setelah login.</div>
            <div class="meta">Username: {{ $user->username }}</div>
        </div>
    </body>
</html>
