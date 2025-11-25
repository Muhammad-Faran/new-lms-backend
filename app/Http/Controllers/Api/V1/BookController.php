<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Traits\ResourcePermissions;
use Illuminate\Http\Request;
use App\Http\Resources\V1\BookResource;
use App\Http\Resources\V1\BookCollection;
use App\Filters\V1\BookFilter;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    use ResourcePermissions;

    // Provide the key that is used in permissions
    protected $permission_key = 'books';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $filter = new BookFilter();

    $query = Book::query();
    
    $books = $filter->filter($query, $request);

    return new BookCollection($books);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $BookRequest)
{
    \DB::transaction(function () use ($BookRequest) {
        $validatedData = $BookRequest->validated();

        // Create the book
        $book = Book::create($validatedData);
        
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}



    public function show(Book $book)
    {
        return new BookResource($book);
    }


   public function update(Book $book, BookRequest $BookRequest)
{
    \DB::transaction(function () use ($book, $BookRequest) {
        $validatedData = $BookRequest->validated();

        // Update the book
        $book->update($validatedData);

    });

    return new BookResource($book);
}



    public function destroy(Book $book)
{
    \DB::transaction(function () use ($book) {
        // Now delete the book
        $book->delete();
    });

    return response()->json(
        [
            "success" => true,
            "data" => [],
        ],
        200
    );
}


}
