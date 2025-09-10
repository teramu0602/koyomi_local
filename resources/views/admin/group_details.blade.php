@extends('layouts.admin_calender')

@section('title', 'イベント詳細')
@section('css')
<link href="{{ asset('css/common.admin.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="w600">
    <h1 class="h1">スケジュール</h1>
    
    <div class="gname">
    <div>
        @forelse ($event->groups as $group)
            <span class="text-bg">{{ $group->group_name }}の予定表</span>
        @empty
            <span class="text-bg"></span> {{-- 空の場合用 --}}
        @endforelse
    </div>

    @php
        $canEdit = $event->groups->isEmpty() || $event->groups->contains(function ($group) {
            return $group->edit_flg == 1;
        });
    @endphp

    @if ($canEdit)
        <div class="button-group">
            <form action="{{ route('group.edit', ['id' => $event->id]) }}" method="get" style="display:inline;">
                <button type="submit" class="btn btn-primary">編集</button>
            </form>
            <form action="{{ route('group.destroy', ['id' => $event->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primaryD">削除</button>
            </form>
        </div>
    @endif
    </div>
    <div class="left">        
        <p>
            日付： {{ \Carbon\Carbon::parse($event->event_start_date)->format('Y年m月d日') }}
            @if ($event->event_end_date)
                ~{{ \Carbon\Carbon::parse($event->event_end_date)->format('Y年m月d日') }}
            @endif
        </p>
        <p>
            時間：
            {{ \Carbon\Carbon::parse($event->event_start_time)->format('H:i') }}~
            {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }}
        </p>
        <p>作成者: {{ $event->user->name }}</p>
        <div class="form-group">
            <p>タイトル</p>
            <input type="text" value="{{ $event->title }}" readonly class="form-control">
        </div>
        <div class="form-group">
            <p>内容</p>
            <textarea readonly class="form-control textarea">{{ $event->content ?? '（説明はありません）' }}</textarea>
        </div>
    </div>
    @forelse ($event->groups as $group)
        <a href="{{ route('group.home', ['id' => $group->id]) }}">戻る</a>
    @empty
        <a href="/">戻る</a>
    @endforelse
</div>
@endsection