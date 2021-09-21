<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "id" => "numeric|nullable",
                "name" => "string|required",
                "email" => "email|required",
                "password" => "string|nullable"
            ]);
            $user = User::query()
                ->findOrNew($request->post("id"))
                ->forceFill([
                    "name" => $request->post("name"),
                    "email" => $request->post("email"),
                ]);
            if (!$request->post("id") && !$request->post("password")) {
                throw new \Exception("Password Required", 404);
            }
            if ($request->post("password")) {
                $user->password = Hash::make($request->post("password"));
            }
            $user->saveOrFail();
            DB::commit();
            return response()->json([
                "message" => "Successfully Done"
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw  $exception;
        }
    }

    private function items(Request $request): Builder
    {
        return User::query()
            ->when($request->post("filter"), function (Builder $builder) use ($request) {
                $builder
                    ->where("id", "=", $request->post("filter"))
                    ->orWhere("name", "like", "%" . $request->post("filter") . "%")
                    ->orWhere("email", "like", "%" . $request->post("filter") . "%");
            })
            ->select([
                "id",
                "name",
                "email",
                "created_at",
                "updated_at"
            ]);
    }

    public function index(Request $request): LengthAwarePaginator
    {
        return
            $this
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
            User::query()
                ->whereIn("id", $request->post("id"))
                ->get()
                ->each(fn(User $i) => $i->deleteOrFail());
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
