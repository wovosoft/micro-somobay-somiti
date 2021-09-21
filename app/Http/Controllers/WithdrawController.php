<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WithdrawController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|nullable",
                "member_id" => "numeric|required",
                "date" => "date_format:Y-m-d|required",
                "amount" => "numeric|required|min:0",
                "description" => "string|nullable",
                "type" => "in:monthly,onetime,others",
            ]);
            Withdraw::query()
                ->findOrNew($request->post("id"))
                ->forceFill([
                    "member_id" => $request->post("member_id"),
                    "date" => $request->post("date"),
                    "amount" => $request->post("amount"),
                    "description" => $request->post("description"),
                    "type" => $request->post("type"),
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
        return Withdraw::query()
            ->select([
                "withdraws.*",
                DB::raw("members.name"),
                DB::raw("members.membership_no"),
                DB::raw("members.pf_index"),
            ])
            ->when($request->post("member_id"), function (Builder $builder) use ($request) {
                $builder->where("withdraws.member_id", "=", $request->post('member_id'));
            })
            ->when($request->post("type"), function (Builder $builder) use ($request) {
                $builder->where("withdraws.type", "=", $request->post('type'));
            })
            ->when($request->post("starting_date") ?? $request->post("ending_date"), function (Builder $builder) use ($request) {
                if ($request->post("starting_date") && $request->post("ending_date")) {
                    $builder->whereBetween("withdraws.date", [$request->post('starting_date'), $request->post("ending_date")]);
                } else {
                    $builder->whereDate("withdraws.date", "=", $request->post("starting_date") ?? $request->post("ending_date"));
                }
            })
            ->leftJoin("members", "members.id", "=", "withdraws.member_id");
    }

    public function index(Request $request): LengthAwarePaginator
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
                "id" => "array|required",
                "id.*" => "numeric|required",
            ]);
            Withdraw::query()
                ->whereIn("id", $request->post("id"))
                ->get()
                ->each(fn (Withdraw $i) => $i->deleteOrFail());
            DB::commit();
            return response()->json([
                "message" => "Successfully Done"
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function export(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "member_id" => "numeric|nullable"
            ]);
            if ($validator->fails()) {
                return $validator->errors();
            }
            return view("pages.withdraws", [
                "member" => Member::query()->find($request->post("member_id")),
                "withdraws" => $this->items($request)->get()
            ]);
        } catch (\Throwable $exception) {
            return $exception->getMessage();
        }
    }
}
