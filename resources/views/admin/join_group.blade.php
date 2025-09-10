@extends('layouts.admin_calender')
@section('title','グループ参加')
@section('css')
<link href="{{ asset('css/common.header.css') }}" rel="stylesheet">
<link href="{{ asset('css/common.admin.css') }}" rel="stylesheet">
@endsection
@section('content')
<h1 class="h1">グループに参加</h1>
<div class="joinctn">
    <form class="form" action="{{ route('join.store') }}" method="POST">
        @csrf
        <div>
            <label for="group_name">グループ名</label>
            </br>
            <input type="text" id="group_name" name="group_name" required>
        </div>

        <div>
            <label for="join_id">参加パスワード</label>
            </br>
            <input type="number" id="join_id" name="join_id" required>
        </div>

</div>
        <button type="submit">参加する</button>
        <div>
            <a href="{{ url()->previous() }}" style="margin-top:15px; display:inline-block;">戻る</a>
        </div>
        @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
        @endif

        @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
        @endif
    </form>

@endsection