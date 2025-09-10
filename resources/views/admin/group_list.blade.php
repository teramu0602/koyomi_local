@extends('layouts.admin_calender')
@section('title', 'グループリスト')
@section('css')
<link href="{{ asset('css/common.header.css') }}" rel="stylesheet">
<link href="{{ asset('css/common.admin.css') }}" rel="stylesheet">
@endsection


@section('content')
<h1 class="h1">グループリスト</h1>
<div class="btn-container">
    <div class="ctn">
        <form action="{{ route('groupjoin') }}" method="GET" style="display: inline;">
            <button class="btn" onclick="toggleMode()">参加</button>
        </form>
        <form action="{{ route('groupcreate') }}" method="GET" style="display: inline;">
            <button class="btn" onclick="toggleMode()">作成</button>
        </form>
    </div>
</div>
<!-- データがある場合はリスト表示、ない場合はメッセージ表示 -->
@if ($userGroups->isEmpty())

<p>まだグループがありません。</p>
@else
<ul class="list-container">
    @foreach ($userGroups as $userGroup)
    @php $group = $userGroup->group; @endphp
    <li>
        <span class="group-name">
            <a class="none" href="{{ route('group.home', ['id' => $group->id]) }}">
                {{ $group->group_name }}
                @if ($userGroup->owner_flg == 1)
                    <span style="color: gold;">★</span>
                @endif
            </a>
        </span>
        <div class="button-container">
            <form action="{{ route('groups.edit', $group->id) }}" method="GET" style="display:inline;">
                <button type="submit" class="btn">詳細</button>
            </form>
            <form action="{{ route('groups.leave', $group->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn1" onclick="return confirm('本当に退会しますか？');">退会</button>
            </form>
        </div>
    </li>
    @endforeach
    <p class="plist">※自身で作成したグループのみ詳細からグループ設定を変更することが出来ます。</p>
    <p class="plist" style="margin-top: 0;">※<span style="color: gold;">★</span>マークはグループオーナーであることを示します。</p>
</ul>
@endif


@php
    $backUrl = session('group_calendar_back_url');
    $isInvalid = false;

    if ($backUrl && preg_match('#/group_home/(\d+)#', $backUrl, $matches)) {
        $groupId = $matches[1];
        $groupExists = \App\Models\Group::find($groupId);
        $deletedGroups = session('deleted_groups', []);
        $isDeleted = in_array($groupId, $deletedGroups);

        if (!$groupExists || $isDeleted) {
            $isInvalid = true;
        }
    }
@endphp

@if ($backUrl)
    <a href="{{ $isInvalid ? route('calendar') : $backUrl }}">戻る</a>
    @else
    <a href="{{  route('calendar') }}">戻る</a>
@endif

<a href="{{ route('calendar') }}" class="floating-btn">
    ⌂
</a>

    @if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
    @endif
@endsection