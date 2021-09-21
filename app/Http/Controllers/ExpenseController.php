<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|nullable",
                "date" => "date_format:Y-m-d|required",
                "description" => "string|nullable",
                "amount" => "numeric|required|min:0",
                "type" => "in:annual_meeting,general_meeting,special_meeting,others",
            ]);
            Expense::query()
                ->findOrNew($request->post("id"))
                ->forceFill([
                    "date" => $request->post("date"),
                    "description" => $request->post("description"),
                    "amount" => $request->post("amount"),
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
        return Expense::query()
            ->select(["*"])
            ->when($request->post('type'), function (Builder $builder) use ($request) {
                $builder->where("type", "=", $request->post("type"));
            })
            ->when($request->post("starting_date") ?? $request->post("ending_date"), function (\Illuminate\Database\Eloquent\Builder $builder) use ($request) {
                if ($request->post("starting_date") && $request->post("ending_date")) {
                    $builder->whereBetween("expenses.date", [$request->post('starting_date'), $request->post("ending_date")]);
                } else {
                    $builder->whereDate("expenses.date", "=", $request->post("starting_date") ?? $request->post("ending_date"));
                }
            });
    }

    public function export(Request $request)
    {
        return view("pages.expenses", [
            "items" => $this->items($request)->get()
        ]);
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
            Expense::query()
                ->whereIn("id", $request->post("id"))
                ->each(fn(Expense $expense) => $expense->deleteOrFail());
            DB::commit();
            return response()->json([
                "message" => "Successfully Done"
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
