@extends('layouts.admin_calender')
@section('title', 'スケジュール作成')
@section('content')


<div class="w600">
  <h1>スケジュールを作成</h1>

    @if(session('success'))
      <p style="color: green;">{{ session('success') }}</p>
    @endif
  <form action="{{ route('group.schedule.store') }}" method="POST">
    @csrf
    <div class="left">
    <p class="text-bg">{{ $group_name }}の予定表</p>
        <p>
            日付　
            開始：<input type="date" name="start_date" class="w70in">　　
            終了：<input type="date" name="end_date" class="w70in">
        </p>
        <p>
            時間　
            開始：<input type="time" name="start_time" class="w70in">　　
            終了：<input type="time" name="end_time" class="w70in">
        </p>
        <div class="form-group">
            <p>タイトル</p>
            <input type="text" name="title" class="form-control">
        </div>
        <div class="form-group">
            <p>内容</p>
        <textarea name="content" class="form-control textarea"></textarea>
        </div>
    </div>
    <input type="hidden" name="group_id" value="{{$group_id}}">
    <button type="submit" class="p-bottom">作成する</button>
    <input type="hidden" name="color" id="color">
  </form>
  <a href="{{ url()->previous() }}">戻る</a>
  <script>
        document.addEventListener("DOMContentLoaded", function () {
            const getPastelColor = () => {
                const colorType = ['yellow', 'green', 'blue'];
                const pick = colorType[Math.floor(Math.random() * colorType.length)];

                let r = 200, g = 200, b = 200; // 初期値

                if (pick === 'yellow') {
                    r = 255;
                    g = 255;
                    b = Math.floor(Math.random() * 100); // 少しだけ青
                } else if (pick === 'green') {
                    r = Math.floor(Math.random() * 100) + 100; // 中程度の赤
                    g = 255;
                    b = Math.floor(Math.random() * 100); // 青少なめ
                } else if (pick === 'blue') {
                    r = Math.floor(Math.random() * 100); // 赤少なめ
                    g = Math.floor(Math.random() * 150) + 100; // 中〜高の緑
                    b = 255;
                }

                return `rgb(${r}, ${g}, ${b})`;
            };

            const color = getPastelColor();
            document.getElementById('color').value = color;
        });
    </script>
    @if ($errors->any())
    <div class="alert alert-danger" style="color: red;">
        <ul style="list-style-type: none; padding-left: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>

@endsection