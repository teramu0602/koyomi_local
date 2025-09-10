@extends('layouts.admin_calender')

@section('title', 'イベント編集')

@section('content')
    <div class="w600">
    <h1 class="h1">スケジュールを編集</h1>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('group.update', ['id' => $event->id]) }}" method="POST">
        @csrf
        @method('PUT')



        <div class="left">
            @foreach ($event->groups as $group)
                <p class="text-bg">{{ $group->group_name }} の予定表</p>
            @endforeach
            <p>
                日付　
                開始：<input type="date" name="event_start_date" value="{{ old('event_start_date', $event->event_start_date) }}" class="w70in" required>　　
                終了：<input type="date" name="event_end_date" value="{{ old('event_end_date', $event->event_end_date) }}" class="w70in" required>
            </p>
            <p>
                時間　
                開始：<input type="time" name="event_start_time" value="{{ old('event_start_time', $event->event_start_time) }}" class="w70in" required>　　    
                終了：<input type="time" name="event_end_time" value="{{ old('event_end_time', $event->event_end_time) }}" class="w70in" required>
            </p>
            <div class="form-group">
                <p>タイトル</p>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <p>内容</p>
            <textarea name="content" class="form-control textarea" required>{{ old('content', $event->content) }}</textarea>
            </div>
        </div>
        <button type="submit" class="p-bottom">更新する</button>

    </form>
    <a href="{{ url()->previous() }}">戻る</a>
</div>
@endsection