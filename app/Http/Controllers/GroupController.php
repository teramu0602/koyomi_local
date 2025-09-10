<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Calendar; 
use Carbon\Carbon;

class GroupController extends Controller
{
    public function edit($groupId)
    {
        //$group = UserGroup::with(['users', 'group', 'user_groups'])->findOrFail($groupId);
            
        $group = Group::with(['users'])
            ->where('id', $groupId)
            ->firstOrFail();

        // ログインしているユーザーのIDを取得
        $userId = auth()->id();
        $user_group = UserGroup::where('user_id', $userId)->where('group_id', $groupId)->firstOrFail();
        $is_owner = $user_group->owner_flg == 1;

        return view('admin.edit_group', compact('group', 'is_owner'));
    }

    public function update(Request $request, $groupId)
{
    $request->validate([
        'group_name' => 'required|string|max:255',
        'edit_flg' => 'required|boolean',
    ]);

    $group = Group::findOrFail($groupId);

    // オーナーのみ変更可能
    $userId = auth()->id();
    $user_group = UserGroup::where('user_id', $userId)->where('group_id', $groupId)->first();

    if (!$user_group || $user_group->owner_flg !== 1) {
        return redirect()->back()->with('error', 'グループ名を変更する権限がありません。');
    }

    $group->group_name = $request->input('group_name');
    $group->edit_flg = (int)$request->input('edit_flg');
    $group->save();

    return redirect()->back()->with('success', 'グループ情報を更新しました。');
}

// ユーザーの削除を行う
public function removeUser($groupId, $userId)
{
    $group = Group::findOrFail($groupId);

    // オーナーのみが削除可能
    $authUserId = auth()->id();
    $authUserGroup = UserGroup::where('user_id', $authUserId)->where('group_id', $groupId)->first();

    if (!$authUserGroup || (int)$authUserGroup->owner_flg !== 1) {
        return redirect()->back()->with('error', 'ユーザーを削除する権限がありません。');
    }

    // 削除対象のユーザーが存在するか確認
    $userGroup = UserGroup::where('user_id', $userId)->where('group_id', $groupId)->first();

    if (!$userGroup) {
        return redirect()->back()->with('error', '指定されたユーザーはグループに存在しません。');
    }

    // ユーザーをグループから削除
    $userGroup->delete();

    return redirect()->back()->with('success', 'ユーザーをグループから削除しました。');
}

public function leaveGroup($groupId)
{
    $userId = auth()->id(); // ログインユーザーのID取得
    $backUrl = session('group_calendar_back_url');
    $redirectUrl = route('groups.list'); // デフォルトはグループ一覧

    $userGroup = UserGroup::where('user_id', $userId)->where('group_id', $groupId)->first();

    if (!$userGroup) {
        return redirect()->back()->with('error', 'グループに参加していません。');
    }

    $groupWillBeDeleted = false;

    if ($userGroup->owner_flg == 1) {
        $otherMembers = UserGroup::where('group_id', $groupId)
                                ->where('user_id', '!=', $userId)
                                ->get();

        if ($otherMembers->isNotEmpty()) {
            $newOwner = $otherMembers->first();
            $newOwner->owner_flg = 1;
            $newOwner->save();
        } else {
            $groupWillBeDeleted = true;
        }
    }

    $userGroup->delete();

    if ($groupWillBeDeleted) {
        Group::where('id', $groupId)->delete();

        // ✅ 削除されたグループIDをセッションに記録
        $deletedGroups = session('deleted_groups', []);
        $deletedGroups[] = $groupId;
        session(['deleted_groups' => $deletedGroups]);
    }

    // ✅ 戻り先URLを削除（残さない方が安全）
    session()->forget('group_calendar_back_url');

    // ✅ 削除されていなければ元のURLに戻る、それ以外はカレンダー
    if ($backUrl && str_contains($backUrl, "group_id={$groupId}")) {
        $redirectUrl = $groupWillBeDeleted ? route('calendar') : $backUrl;
    }

    return redirect($redirectUrl)->with('success', 'グループから退会しました。');
}


public function show($id, $year = null, $month = null)
{
    $group = Group::findOrFail($id);

    $year = $year ?? Carbon::now()->year;
    $month = $month ?? Carbon::now()->month;

    // 該当グループの予定だけ取得（中間テーブルから絞り込む）
    $post = Calendar::whereHas('groups', function ($query) use ($id) {
        $query->where('group_id', $id);
    })
    ->get();
    $events = Calendar::whereYear('event_start_date', $year)
        ->whereMonth('event_start_date', $month)
        ->whereHas('groups', function ($query) use ($id) {
            $query->where('group_id', $id);
        })
        ->get();

    $schedules = $events->groupBy(function ($schedule) {
        return date('j', strtotime($schedule->event_start_date));
    });

    return view('calender.group_home', compact('group','year', 'month','post','schedules','events'));
}

public function showEvent($id)
{
    $event = Calendar::with('user', 'calendar_groups.group')->findOrFail($id);
    return view('admin.group_details', compact('event'));
}



}