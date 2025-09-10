<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class CalendarController extends Controller
{


    public function showAdmin($year, $month)
    {
        return view('calender.home', compact('year', 'month'));
    }

    //お試しコード


    //グループ作成データの保存
    public function store(Request $request)
    {

        //デバッグ
        \Log::info($request->all());


        $request->validate([
            'group_name' => 'required|string|max:30|unique:groups,group_name',
            'join_id' => 'required|string|max:10',
            'edit_flg' => 'nullable|boolean'
        ], [
            'group_name.max' => 'グループ名は30文字以内で入力してください。',
            'join_id.max' => '参加IDは10文字以内で入力してください。',
            'group_name.unique' => 'このグループ名はすでに使用されています。',
        ]);

        // デバッグ用に確認
        \Log::info("Validated data: ", $request->only(['group_name', 'join_id', 'edit_flg']));

        $group = Group::create([
            'group_name' => $request->group_name,
            'join_id' => $request->join_id,  // 入力されたjoin_idを保存
            'edit_flg' => $request->has('edit_flg') ? 1 : 0,  // チェックボックスがある場合のみ1
        ]);

        // グループ作成者（ログイン中のユーザー）をオーナーとして設定
        if ($group) {
            // グループ作成者（ログイン中のユーザー）をオーナーとして設定
            UserGroup::create([
                'user_id' => auth()->id(),  // ログインしているユーザーのID
                'group_id' => $group->id,   // 作成したグループのID
                'owner_flg' => 1,           // オーナーフラグを1に設定（オーナーとして扱う）
            ]);

            return redirect()->route('groups.list')->with('success', 'グループが作成されました！');
        }


        return back()->with('error', 'グループ作成に失敗しました。');
    }


    //showとindexは月を変える矢印のリンクがあるので、両方同じようにしないといけない
    public function show($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;
        // 指定された年月のスケジュールを取得
        $schedules = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->get();
        // 最新の投稿データを1件取得
        $post = Calendar::all(); // もしくは、必要な条件でデータを取得

        $a = Calendar::all();

        $schedules = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->get()
            ->groupBy(function ($schedule) {
                return date('j', strtotime($schedule->date)); // 日ごとにグループ化
            });


        $events = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->get();
        return view('calender.home', compact('year', 'month', 'post', 'schedules', 'a', 'events', 'schedules'));
    }
    public function index($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;
        // 最新の投稿データを1件取得
        $post = Calendar::all(); // もしくは、必要な条件でデータを取得
        $a = Calendar::all();
        $events = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->get();
        $events = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->whereDoesntHave('calendar_groups') // グループに属していないカレンダー
            ->get();
        $events = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->where('user_id', Auth::id())
            ->whereDoesntHave('calendar_groups') // グループ予定に属していない
            ->get();



        $schedules = Calendar::whereYear('event_start_date', $year)
            ->whereMonth('event_start_date', $month)
            ->get()
            ->groupBy(function ($schedule) {
                return date('j', strtotime($schedule->date)); // 日ごとにグループ化
            });

        // ビューにデータを渡す
        return view('calender.home', compact('year', 'month', 'post', 'a', 'events', 'schedules'));
    }
    // public function groupCalendarAdd(Request $request){
    //     return view('admin.create_group_schedule', ['group_id' => $request->group_id]);
    // }
    public function groupCalendarAdd(Request $request)
    {
        $group = Group::findOrFail($request->group_id); // group_id に該当するグループを取得
        return view('admin.create_group_schedule',  [
            'group_id' => $group->id,
            'group_name' => $group->group_name,
        ]);
    }
}