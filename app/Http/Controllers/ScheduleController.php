<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\CalendarGroup;
use App\Models\Group;

class ScheduleController extends Controller
{

    public function create()
    {
        return view('createschedule');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'color'  => 'nullable', //追加　nullableの編集をする！
        ],[
            'title.required'      => 'タイトルは必須です。',
            'start_date.required' => '開始日を入力してください。',
            'end_date.required'   => '終了日を入力してください。',
            'end_date.after_or_equal' => '終了日は開始日と同じか、それ以降の日付を選んでください。',
            'start_time.required' => '開始時間を入力してください。',
            'end_time.required'   => '終了時間を入力してください。',
            'end_time.after'      => '終了時間は開始時間より後にしてください。',
        ]);
        // 日付が同じで、両方の時間が入力されている場合は、時間の整合性をチェック
        if (
            $request->start_date === $request->end_date &&
            $request->start_time !== null &&
            $request->end_time !== null &&
            $request->start_time >= $request->end_time
        ) {
            return back()
                ->withErrors(['end_time' => '終了時間は開始時間より後である必要があります。'])
                ->withInput();
        }
        Calendar::create([
            'user_id' => auth()->id(), // ログインユーザーのID
            'title'   => $request->title,
            'content' => $request->content,
            'event_start_date' => $request->start_date,
            'event_end_date'   => $request->end_date,
            'event_start_time' => $request->start_time,
            'event_end_time'   => $request->end_time,
            'color'  => $request->color,
        ]);
        

        return redirect()->route('calendar')->with('success', 'スケジュールを作成しました！');
    }

    public function storeGroup(Request $request)
    {
        
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'group_id' =>'nullable',
            'color'  => 'nullable', //追加　nullableの編集をする！
        ],[
            'title.required'      => 'タイトルは必須です。',
            'start_date.required' => '開始日を入力してください。',
            'end_date.required'   => '終了日を入力してください。',
            'end_date.after_or_equal' => '終了日は開始日と同じか、それ以降の日付を選んでください。',
            'start_time.required' => '開始時間を入力してください。',
            'end_time.required'   => '終了時間を入力してください。',
            'end_time.after'      => '終了時間は開始時間より後にしてください。',
        ]);
        // 日付が同じで、両方の時間が入力されている場合は、時間の整合性をチェック
        if (
            $request->start_date === $request->end_date &&
            $request->start_time !== null &&
            $request->end_time !== null &&
            $request->start_time >= $request->end_time
        ) {
            return back()
                ->withErrors(['end_time' => '終了時間は開始時間より後である必要があります。'])
                ->withInput();
        }


        $calendar = new Calendar;
        $form = $request->all();
        $calendar['user_id'] = auth()->id();
        $calendar['title'] = $request->title;
        $calendar['content'] = $request->content;
        $calendar['event_start_date'] = $request->start_date;
        $calendar['event_end_date'] = $request->end_date;
        $calendar['event_start_time'] = $request->start_time;
        $calendar['event_end_time'] = $request->end_time;
        $calendar['color'] = $request->color;

        $calendar->save();

        CalendarGroup::create([
            'calendar_id' => $calendar['id'],
            'group_id' => $request->group_id

        ]);
        return redirect()->route('group.home', ['id' => $request->group_id])
        ->with('success', 'スケジュールを作成しました。');
    }


    public function s_edit($id)
    {
        $event = Calendar::with('groups')->findOrFail($id);
        $canEdit = $event->groups->isEmpty() || $event->groups->contains(function ($group) {
            return $group->edit_flg == 1;
        });
    
        if (!$canEdit) {
            return redirect()->back()->with('error', 'このイベントは編集できません。');
        }
    
        return view('admin.edit_schedule', compact('event'));
    }
    


    
    public function s_update(Request $request, $id)
    {
        $event = Calendar::with('groups')->findOrFail($id);
    
        // $canEdit = $event->groups->contains(function ($group) {
        //     return $group->edit_flg == 1;
        // });
    
        // if (!$canEdit) {
        //     return redirect()->back()->with('error', 'このイベントは編集できません。');
        // }
    
        $request->validate([
            'event_start_date' => 'required|date',
            'event_start_time' => 'required',
            'event_end_date' => 'nullable|date',
            'event_end_time' => 'nullable',
            'content' => 'nullable|string|max:1000',
            'title' =>'required',
        ]);
    
        $event->update([
            'event_start_date' => $request->event_start_date,
            'event_start_time' => $request->event_start_time,
            'event_end_date' => $request->event_end_date,
            'event_end_time' => $request->event_end_time,
            'content' => $request->content,
            'title' => $request->title,
        ]);
        \Log::info('更新後: ', $event->toArray());
        $groupId = $event->groups->first()->id ?? null;

if ($groupId) {
    return redirect()->route('group.home', ['id' => $groupId])->with('success', 'イベント内容を更新しました。');
} else {
    return redirect()->back()->with('error', 'グループ情報が見つかりませんでした。');
}

    }

    public function destroy($id)
{
    $event = Calendar::with('groups')->findOrFail($id);

    // 編集可能な権限チェック
    $canEdit = $event->groups->isEmpty() || $event->groups->contains(function ($group) {
        return $group->edit_flg == 1;
    });

    if (!$canEdit) {
        return redirect()->back()->with('error', 'このイベントは削除できません。');
    }

    $event->delete();

    // リダイレクト先を分岐
    if ($event->groups->isNotEmpty()) {
        $groupId = $event->groups->first()->id; // 最初のグループIDを取得
        return redirect()->route('group.home', ['id' => $groupId])
                         ->with('success', 'イベントを削除しました。');
    } else {
        return redirect()->route('calendar')
                         ->with('success', 'イベントを削除しました。');
    }
}

    
}