@extends('layouts.admin_calender')

@section('title', 'home')


@section('drop_menu')
<p>
    @guest
    @if (Route::has('login'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
    </li>
    @endif

    @if (Route::has('register'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
    </li>
    @endif
    @else

    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ Auth::user()->name }}
        </a>

        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                {{ __('ログアウト') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>


        </div>
    </li>
    @endguest
</p>
@endsection

@section('header')
<p>カレンダー</p>
@endsection

@section('content')
@php
// URLパラメータから年月を取得（なければ現在の年月）
$year = request('year', date('Y'));
$month = request('month', date('n'));
$weekday = request('weekday', date('w'));

// 前月・次月の計算
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth=12;
    $prevYear--;
}

// 曜日を取得 前月次月の計算に使いたい


$nextMonth=$month + 1;
$nextYear=$year;
if ($nextMonth> 12) {
    $nextMonth = 1;
    $nextYear++;
}

// 月の情報を取得
$firstDay = strtotime("$year-$month-1");
$lastDay = date('t', $firstDay);
$lastDayTimestamp = strtotime("$year-$month-$lastDay");
$startWeekday = date('w', $firstDay);
$lastWeekday = date('w', $lastDayTimestamp);

$firstDayOfPreviousMonth = strtotime("first day of last month"); // 前月の1日目
$lastDayOfPreviousMonth = date('t', strtotime("$prevYear-$prevMonth-01")); // 前月の最終日

$firstDayOfPreviousMonth = strtotime("last day of last month"); // 前月の1日目
$firstDayOfPreviousMonthFormatted = date('j', $firstDayOfPreviousMonth); // 日付をフォーマットして取得

// 曜日を取得 前月次月の計算に使いたい
$lastmonthday = $lastDayOfPreviousMonth-$startWeekday+1;
$nextmonthday = 1;


// カレンダー配列を作成
$calendar = [];
$row = [];
for ($i = 0,$lastmonthday=$lastmonthday-$startWeekday; $i < $startWeekday; $i++) {
    $row[] = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $lastmonthday++);
}

for ($day = 1; $day <= $lastDay; $day++) {
    $row[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
    if (count($row) == 7) {
        $calendar[] = $row;
        $row = [];
    }
}

for ($i = 0, $day = 1; $i < 6 - $lastWeekday; $i++, $day++) {
    $row[] = sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day);
}
$calendar[] = $row;

// 翌月の最初の数日をグレーにする
$nextMonthDays=range($lastmonthday-$weekday-1, $lastDayOfPreviousMonth);
@endphp



<div class="calendar_menu1">
        <div class="calendar_menu_left">
            <a class="who2">{{ $group->group_name }}の予定表</a>
        </div>
        <div class="calendar_menu_center">
            <a class="month_toggle" href="{{ url('/group_home/' . $group->id . '/'. $prevYear . '/' . $prevMonth) }}">◀</a>
            　{{ $year }}年　{{ $month }}月
            <a class="month_toggle" href="{{ url('/group_home/' . $group->id . '/'. $nextYear . '/' . $nextMonth) }}">▶</a>
        </div>
    
        <div class="calendar_menu_right">
            <form action="{{ route('calendar') }}" method="GET" style="display: inline;">
                <button type="submit" class="switch_button2 {{ request()->is('personal') ? 'active' : '' }}">ホーム</button>
            </form>
            <form action="{{ route('groups.list') }}" method="GET" style="display: inline;">
                <input type="hidden" name="from" value="{{ url()->full() }}">
                <button type="submit" class="btn">グループ</button>
            </form>
            <form action="{{ route('groupCalendarAdd', ['group_id' => $group->id]) }}" method="GET" style="display: inline;">
                <button type="submit" >予定作成</button>
            </form>
        </div>
</div>




<div class="calendar-wrapper">
    <table class="calendar-table">
        <tr>
            <th class="red">日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th class="blue">土</th>
        </tr>
        @foreach ($calendar as $week)
        <tr>
            @foreach ($week as $day)
            @php
            $class = "";
    
            if ($day !== "") {
                $dayDate = \Carbon\Carbon::parse($day); // Carbonで日付を扱いやすくする
                $dayYear = $dayDate->year;
                $dayMonth = $dayDate->month;
                $dayDay = $dayDate->day;
    
                // 前月の日付の判定
                if ($loop->parent->first && ($dayMonth != $month)) {
                    $class = "prev-month";
                }
                // 翌月の日付の判定
                elseif ($loop->parent->last && ($dayMonth != $month)) {
                    $class = "next-month";
                }
                // 日曜（赤）
                elseif ($loop->index % 7 == 0) {
                    $class = "sunday";
                }
                // 土曜（青）
                elseif ($loop->index % 7 == 6) {
                    $class = "saturday";
                }
            }
    
            @endphp
            <td class="{{ $class }}" style="position: relative;" >
                @php
                    // 指定した日付に該当するイベントのみを取得
                    $e = $post->filter(function($event) use ($day) {
                        return $event->event_start_date === $day
                        && Auth::check(); // ログインしているか確認
    
                    });
                @endphp
                <div class="date">{{ (int)substr($day, 8, 2) }}</div>
                {{-- イベント数のバッジ（0件は表示しない） --}}
                @if ($e->count() > 0)
                    <div class="event-count-badge">
                        {{ $e->count() }}
                    </div>
                @endif
                <div class = "calendar_title">
                    @foreach($e as $event)
                        <div style="background-color: {{ $event->color }}; padding: 2px; margin-bottom: 2px; border-radius: 4px; color: #fff;">
                            <a href="{{ route('group.details', ['id' => $event->id]) }}" style="color: black; text-decoration: none;">
                                {{ $event->title }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>
</div>
<a href="{{ route('calendar') }}" class="floating-btn">
    ⌂
</a>
@endsection