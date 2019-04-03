<?php
// made with php artisan make:controller BookController
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return 'all your books...';
    }
    
       /**
    * GET /books/{title}
    */
    public function show($title)
    {
        return view('books.show')->with(['title' => $title]);
    }
    
        /**
    * GET
    * /books/search
    * Show the form to search for a book
    * Added for week 8
    */
    public function search() 
    {
        return view('books.search')->with([
        'searchTerm' => $request->session()->get('searchTerm', ''),
        'caseSensitive' => $request->session()->get('caseSensitive', false),
        'searchResults' => $request->session()->get('searchResults', []),
        ]);
    }

    /**
    * GET
    * /books/search-process
    * Process the form to search for a book
    */
    public function searchProcess(Request $request) {

        # Start with an empty array of search results; books that
        # match our search query will get added to this array
        $searchResults = [];

        # Store the searchTerm in a variable for easy access
        # The second parameter (null) is what the variable
        # will be set to *if* searchTerm is not in the request.
        $searchTerm = $request->input('searchTerm', null);

        # Only try and search *if* there's a searchTerm
        if ($searchTerm) {
            # Open the books.json data file
            # database_path() is a Laravel helper to get the path to the database folder
            # See https://laravel.com/docs/helpers for other path related helpers
            $booksRawData = file_get_contents(database_path('/books.json'));

            # Decode the book JSON data into an array
            # Nothing fancy here; just a built in PHP method
            $books = json_decode($booksRawData, true);

            # Loop through all the book data, looking for matches
            # This code was taken from v0 of foobooks we built earlier in the semester
            foreach ($books as $title => $book) {
                # Case sensitive boolean check for a match
                if ($request->has('caseSensitive')) {
                    $match = $title == $searchTerm;
                # Case insensitive boolean check for a match
                } else {
                    $match = strtolower($title) == strtolower($searchTerm);
                }

                # If it was a match, add it to our results
                if ($match) {
                    $searchResults[$title] = $book;
                }
            }
        }

        # Redirect back to the search page w/ the searchTerm *and* searchResults (if any) stored in the session
        # Ref: https://laravel.com/docs/redirects#redirecting-with-flashed-session-data
        return redirect('/books/search')->with([
            'searchTerm' => $searchTerm,
            'caseSensitive' => $request->has('caseSensitive'),
            'searchResults' => $searchResults
        ]);
}
    /**
    * GET /books/create
    * Display the form to add a new book
    */
    public function create(Request $request) 
    {
        return view('books.create');
    }


    /**
    * POST /books
    * Process the form for adding a new book
    */
    
    
    public function store(Request $request) {

    # Validate the request data
    $request->validate([
       'title' => 'required',
       'author' => 'required',
       'published_year' => 'required|digits:4',
       'cover_url' => 'required|url',
       'purchase_url' => 'required|url',
    ]);

    # Note: If validation fails, it will redirect the visitor back to the form page
    # and none of the code that follows will execute.

    # Code will eventually go here to add the book to the database,
    # but for now we'll just dump the form data to the page for proof of concept
    dump($request->all());
}
    //old...
    /**
    * GET
    * /books/search-process
    * Process the form to search for a book
    * Renamed method with V0. Added for week 8
    */
    
    public function searchProcessV0(Request $request) {

    # ======== Temporary code to explore $request ==========

    # See all the properties and methods available in the $request object
    dump($request);

    # See just the form data from the $request object
    dump($request->all());

    # See just the form data for a specific input, in this case a text input
    dump($request->input('searchTerm'));
    
    # See what the form data looks like for a checkbox
    dump($request->input('caseSensitive'));
    
    # Form data can also be accessed via dynamic properties
    dump($request->searchTerm);

    # Boolean to see if the request contains data for a particular field
    dump($request->has('searchTerm')); # Should be true
    dump($request->has('publishedYear')); # There's no publishedYear input, so this should be false

    # You can get more information about a request than just the data of the form, for example...
    dump($request->path()); # "books/search-process"
    dump($request->is('books/search-process')); # True
    dump($request->is('search')); # False
    dump($request->fullUrl()); # e.g. http://foobooks.loc/books/search-process?searchTerm=abc
    dump($request->method()); # GET
    dump($request->isMethod('post')); # False

    # ======== End exploration of $request ==========
    }
}
