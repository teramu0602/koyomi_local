@extends('layouts.admin_calender')
@section('title', 'sign up')

@section('content')
<h2 style="margin-top:70px;">アカウントの作成</h2>
<form action="{{ route('register') }}" method="POST">
    @csrf
    <label for="name">ユーザー名</label>
    <input type="text" id="name" name="name" placeholder="ユーザー名" required>

    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" placeholder="パスワード" required>

    <label for="password_confirmation">確認</label>
    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="確認" required>

    <button type="submit">アカウントを作成する</button>
</form>

<p class="login-link">
    <a href="/custom-login">アカウントをお持ちの方はこちら</a>
</p>
@endsection