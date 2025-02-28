<?php

namespace App\Http\Controllers;

use App\Http\Resources\BorrowingResource;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BorrowingController extends Controller
{
    public function index()
    {
        $data = Borrowing::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function borrowing(Request $request)
    {
        $validation = $request->validate([
            'book_id' => ['required', 'integer', Rule::exists('books', 'id')],
            'loan_duration' => 'required|min:1|integer'
        ]);

        $validation['loan_duration'] = (int) $validation['loan_duration'];
        
        $data = Borrowing::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'loan_duration' => $request->loan_duration,
            'borrowed_at' => Carbon::now(),
            'return_due_at' => Carbon::now()->addDays($validation['loan_duration']),
            'status' => 'borrowed'
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Book borrowing data successfully added",
            'data' => new BorrowingResource($data)
        ], 200);
    }

    public function returned(string $id)
    {
        $data = Borrowing::where('id',$id)->first();
        
        if (!$data) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found",
            ], 404);
        }

        $data->status = 'returned';
        $data->save();

        return response()->json([
            "status" => "success",
            "message" => "Book successfully returned",
            "data" => new BorrowingResource($data)
        ], 200);
    }

    public function destroy(string $id)
    {
        $data = Borrowing::find($id);
    

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data deleted'
        ], 200);
    }
}