@extends('layouts.admin_calender')
@section('title', 'グループ作成')

@section('content')

    <h1 class="h1">グループ作成</h1>
    <div class="joinctn">
    <form class="form" action="{{ route('groups.store') }}" method="POST">
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
            </br>
            <p class="p_c">※参加パスワードはグループに参加する際に使用します。</p>
        </div>
        <div class="w400">
            <p>
                <input class = "check_box" type="checkbox" id="edit_flg" name="edit_flg" value="1">
                <label class="label_n" for="edit_flg">他ユーザーの予定を編集、削除出来ないようにする。</label>
            </p>
        </div>
        </div>
        <button type="submit">作成する</button>
    </form>
        <div>
            <a href="{{ url()->previous() }}" style="margin-top:15px; display:inline-block;">戻る</a>
        </div>
        <a href="{{ route('calendar') }}" class="floating-btn">
            ⌂
        </a>    
        @error('group_name')
            <p style="color: red;">{{ $message }}</p>
        @enderror
        @error('join_id')
            <p style="color: red;">{{ $message }}</p>
        @enderror
@endsection