<?php

namespace App\Http\Controllers;

use App\Models\Nominee;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NomineeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|nullable",
                "member_id" => "numeric|required",
                "name" => "string|required",
                "relation" => "string|required",
                "nid_no" => "string|nullable",
                "phone" => "string|nullable",
            ]);
            Nominee::query()
                ->findOrNew($request->post("id"))
                ->forceFill([
                    "member_id" => $request->post("member_id"),
                    "name" => $request->post("name"),
                    "relation" => $request->post("relation"),
                    "nid_no" => $request->post("nid_no"),
                    "phone" => $request->post("phone"),
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

    public function index(Request $request): View|Factory|LengthAwarePaginator|Application
    {
        if ($request->isMethod("POST")) {
            return Nominee::query()
                ->select(["*"])
                ->orderBy($request->input("order_by") ?? "id", $request->input("order") ?? "asc")
                ->paginate(
                    perPage: $request->input("per_page") ?? 15,
                    page: $request->input("page") ?? 1
                );
        }
        return view("welcome");
    }

    public function destroy(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|required"
            ]);
            Nominee::query()
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
}
