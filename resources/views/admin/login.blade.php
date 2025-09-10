@extends('layouts.admin_calender')

@section('title', 'ログイン')

@section('content')
    <h2 style="margin-top:70px;">ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="name">ユーザー名</label>
        <input type="text" id="name" name="name" placeholder="ユーザー名" value="{{ old('name') }}" required autofocus>

        <label for="password">パスワード</label>
        <input type="password" id="password" name="password" placeholder="パスワード" required>

        <div class="checkbox">
            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">ログインしたままにする</label>
        </div>

        <button type="submit">ログイン</button>
    </form>


    <p class="login-link">
        <a href="/signup">アカウントをお持ちでない方はこちら</a>
    </p>
@endsection