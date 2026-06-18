<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\TimKamiPage;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TimKamiController extends Controller
{
    // Public
    public function show()
    {
        return response()->json([
            'hero'           => TimKamiPage::first(),
            'leaders'        => TeamMember::where('division', 'leader')->where('is_active', true)->orderBy('order')->get(),
            'client_support' => TeamMember::where('division', 'client_support')->where('is_active', true)->orderBy('order')->get(),
            'developers'     => TeamMember::where('division', 'developer')->where('is_active', true)->orderBy('order')->get(),
        ]);
    }

    // Admin
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'hero'           => TimKamiPage::first(),
            'leaders'        => TeamMember::where('division', 'leader')->orderBy('order')->get(),
            'client_support' => TeamMember::where('division', 'client_support')->orderBy('order')->get(),
            'developers'     => TeamMember::where('division', 'developer')->orderBy('order')->get(),
        ]);
    }

    // ==================== HERO ====================
    public function storeHero(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'section2_label' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['title', 'description', 'section2_label']);
        $hero = TimKamiPage::create($data);

        ActivityLogger::log($user->id, 'create_tim_kami_hero', 'website', 'Membuat hero Tim Kami');

        return response()->json(['message' => 'Hero berhasil disimpan.', 'data' => $hero], 201);
    }

    public function updateHero(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'section2_label' => 'nullable|string|max:255',
        ]);

        $hero = TimKamiPage::findOrFail($id);
        $hero->update($request->only(['title', 'description', 'section2_label']));
        $hero->refresh();

        ActivityLogger::log($user->id, 'update_tim_kami_hero', 'website', 'Mengupdate hero Tim Kami');

        return response()->json(['message' => 'Hero berhasil diupdate.', 'data' => $hero]);
    }

    // ==================== TEAM MEMBERS ====================
    public function storeMember(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'division'    => 'required|in:leader,client_support,developer',
            'order'       => 'nullable|integer',
        ]);

        $data = $request->only(['photo', 'name', 'designation', 'division', 'order']);
        $data['is_active'] = true;

        $member = TeamMember::create($data);

        ActivityLogger::log($user->id, 'create_team_member', 'website', 'Menambahkan team member', ['member_id' => $member->id, 'division' => $member->division]);

        return response()->json(['message' => 'Team member berhasil ditambahkan.', 'data' => $member], 201);
    }

    public function updateMember(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'division'    => 'required|in:leader,client_support,developer',
            'order'       => 'nullable|integer',
        ]);

        $member = TeamMember::findOrFail($id);

        $data = $request->only(['photo', 'name', 'designation', 'division', 'order']);
        $data['is_active'] = $request->boolean('is_active');

        $member->update($data);
        $member->refresh();

        ActivityLogger::log($user->id, 'update_team_member', 'website', 'Mengupdate team member', ['member_id' => $id]);

        return response()->json(['message' => 'Team member berhasil diupdate.', 'data' => $member]);
    }

    public function destroyMember(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        TeamMember::findOrFail($id)->delete();

        ActivityLogger::log($user->id, 'delete_team_member', 'website', 'Menghapus team member', ['member_id' => $id]);

        return response()->json(['message' => 'Team member berhasil dihapus.']);
    }
}