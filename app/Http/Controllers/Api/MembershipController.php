<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Member;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function getPlans()
    {
        return response()->json(MembershipPlan::all(), 200);
    }

    public function getMembers()
    {
        return response()->json(Member::with('plan')->orderBy('id', 'desc')->get(), 200);
    }

    public function addMember(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'email'         => 'required|email|unique:members',
            'phone'         => 'required|string',
            'plan_id'       => 'required|integer',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date'
        ]);

        $member = Member::create($validated);
        return response()->json($member, 201);
    }

    public function updateStatus(Request $request, int $id)
    {
        $member = Member::find($id);
        if (!$member) return response()->json(['message' => 'Member not found'], 404);

        $member->update(['status' => $request->status]);
        return response()->json($member, 200);
    }

    public function cancelMembership(int $id)
    {
        $member = Member::find($id);
        if ($member) {
            $member->delete();
            return response()->json(['message' => 'Membership cancelled'], 200);
        }
        return response()->json(['message' => 'Member not found'], 404);
    }
}