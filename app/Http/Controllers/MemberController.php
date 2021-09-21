<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|nullable",
                "membership_no" => "string|required",
                "name" => "string|required",
                "pf_index" => "alpha_num|required|min:2|max:8",
                "current_workplace" => "string|nullable",
                "bank_joining_date" => "date_format:Y-m-d|nullable",
                "membership_entry_date" => "date_format:Y-m-d|nullable",
                "home_district" => "string|nullable",
                "nid_no" => "string|nullable",
                "tin_no" => "string|nullable",
                "phone" => "string|nullable",
                "secondary_phone" => "string|nullable",
                "email" => "email|nullable",
            ]);
            Member::query()
                ->findOrNew($request->post("id"))
                ->forceFill([
                    "membership_no" => $request->post("membership_no"),
                    "name" => $request->post("name"),
                    "pf_index" => $request->post("pf_index"),
                    "current_workplace" => $request->post("current_workplace"),
                    "bank_joining_date" => $request->post("bank_joining_date"),
                    "membership_entry_date" => $request->post("membership_entry_date"),
                    "home_district" => $request->post("home_district"),
                    "nid_no" => $request->post("nid_no"),
                    "tin_no" => $request->post("tin_no"),
                    "phone" => $request->post("phone"),
                    "secondary_phone" => $request->post("secondary_phone"),
                    "email" => $request->post("email"),
                ])
                ->saveOrFail();
            DB::commit();
            return response()->json([
                "message" => "Successfully Done"
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw  $exception;
        }
    }
    private function items(Request $request)
    {
        return Member::query()
            ->select(["*"])
            ->when($request->post("filter"), function (Builder $builder) use ($request) {
                $builder
                    ->where("id", "=",  $request->post("filter"))
                    ->orWhere("name", "like", "%" . $request->post("filter") . "%")
                    ->orWhere("pf_index", "like", "%" . $request->post("filter") . "%")
                    ->orWhere("membership_no", "like", "%" . $request->post("filter") . "%")
                    ->orWhere("current_workplace", "like", "%" . $request->post("filter") . "%");
            });
    }
    public function exportbalanceSheet(Request $request)
    {
        return view("pages.balance_sheet", [
            "items" => $this
                ->items($request)
                ->select([
                    "members.*",
                    "deposit_amount" => function (QueryBuilder $builder) use ($request) {
                        $builder
                            ->from("deposits")
                            ->where("deposits.member_id", "=", DB::raw("members.id"))
                            ->selectRaw("SUM(deposits.amount)");

                        if ($request->post("starting_date")) {
                            $builder->whereDate("date", ">=", $request->post("starting_date"));
                        }
                        if ($request->post("ending_date")) {
                            $builder->whereDate("date", "<=", $request->post("ending_date"));
                        }
                    },
                    "withdraw_amount" => function (QueryBuilder $builder) use ($request) {
                        $builder
                            ->from("withdraws")
                            ->where("withdraws.member_id", "=", DB::raw("members.id"))
                            ->selectRaw("SUM(withdraws.amount)");

                        if ($request->post("starting_date")) {
                            $builder->whereDate("date", ">=", $request->post("starting_date"));
                        }
                        if ($request->post("ending_date")) {
                            $builder->whereDate("date", "<=", $request->post("ending_date"));
                        }
                    }
                ])
                ->get()
        ]);
    }
    public function index(Request $request)
    {
        return $this
            ->items($request)
            ->orderBy($request->input("order_by") ?? "id", $request->input("order") ?? "desc")
            ->paginate(
                perPage: $request->input("per_page") ?? 15,
                page: $request->input("page") ?? 1
            );
    }

    public function destroy(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|required"
            ]);
            Member::query()
                ->findOrFail($request->post("id"))
                ->deleteOrFail();
            DB::commit();
            return response()->json([
                "message" => "Successfully Done"
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                "query" => "string|numeric|nullable",
                "limit" => "numeric|nullable"
            ]);
            return Member::query()
                ->where("pf_index", "like", "%" . $request->input("search") . "%")
                ->orWhere("name", "like", "%" . $request->input("search") . "%")
                ->orWhere("membership_no", "like", "%" . $request->input("search") . "%")
                ->orWhere("phone", "like", "%" . $request->input("search") . "%")
                ->limit($request->input("limit") ?? 20)
                ->orderBy("name", "asc")
                ->get();
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function find(Member $member)
    {
        try {
            return $member;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function profile($member)
    {
        try {
            return Member::query()
                ->with(["deposits"])
                ->findOrFail($member)
                ->append(["total_deposit", "total_withdraw"]);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
