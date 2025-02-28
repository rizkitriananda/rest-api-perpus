<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{

    // cek admin
    public function isAdmin(){
        return Auth::user()->role_id === 1;
    }


    public function index()
    {
        $data = BookResource::collection(Book::all());

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    if(!$this->isAdmin()){
        return response()->json([            
            "status" => "insufficient_permissions",
            "message"=> "Access forbidden",
        ], 401);
    }

    $request->validate([
        'title' => 'required|string',
        'genre' => 'required|string',
        'release_year' => 'required|integer',
        'stock' => 'required|integer',
        'cover_image' => 'nullable|image|max:2048|mimes:jpg,png,jpeg'
    ]);

    $book = $request->except('cover_image');

    if($request->hasFile('cover_image')){
        $file = $request->cover_image;
     
        $book['cover_image'] = $file->store('books','public');
    }

    // create
    $data = Book::create($book);

    return response()->json([
        'status' => 'success',
        'message' => 'Created book successfully',
        'data' => $data->makeHidden('id')
    ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::where('id',$id)->first();
        if(!$book){
            return response()->json([
                "status"=> "not_found",
                "message"=> "Resource not found",

            ], 404);              
        }
        $data = new BookResource($book);

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
    // cek validasi admin
    if(!$this->isAdmin()){
        return response()->json([            
            "status" => "insufficient_permissions",
            "message"=> "Access forbidden",
        ], 401);
    }

    // validasi data
    $validation = $request->validate([
        'title' => 'sometimes|string',
        'genre' => 'sometimes|string',
        'release_year' => 'sometimes|integer',
        'stock' => 'sometimes|integer',
        'cover_image' => 'nullable'
    ]);

    // jika ada file pada request
    if($request->hasFile('cover_image')){
       // check apakah ada cover image di table
       if($book->cover_image){
        Storage::disk('public')->delete($book->cover_image);
       }

       // simpan gambar baru 
       $validation['cover_image'] =   $request->file('cover_image')->store('books', 'public');
    }

    // simpan data pada model
    $book->fill($validation);

    // jika tidak ada perubahan data
    if(!$book->isDirty()){
        return response()->json([
            'status' => 'failed',
            'message' => 'No changes detected'
        ], 400);
    }

    // save data ke database
    $book->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Created dvd successfully',
        'data' => $book->makeHidden('id')
    ], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    if(!$this->isAdmin()){
        return response()->json([            
            "status" => "insufficient_permissions",
            "message"=> "Access forbidden",
        ], 401);
    }

    $book = Book::where('id' ,$id)->first();
    if(!$book){
        return response()->json([
            "status"=> "not_found",
            "message"=> "Resource not found",

        ], 404);
    }

    $book->delete();
    return response()->json([
                "status"=> "success",
                "message"=> "Book successfully deleted",

    ], 201);
    }
}
