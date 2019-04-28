<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use IanLChapman\PigLatinTranslator\Parser;
use App\Book; # <----------- week 11: makes Book class accessible in Controller's namespace

# Run practice methods from "foobooks.loc/practice"
class PracticeController extends Controller
{
    # Added in Week 11 -- Eloquent
    public function practice5()
    {
        # Instantiate a new Book Model object
        $book = new Book();
        $books = $book->where('title', 'LIKE', '%Harry Potter%')->get(); 
        
        # Use model as facade to combine the two previous lines:
        #  $books = Book::where('title', 'LIKE', '%Harry Potter%')->get();

        # Set the properties
        # Note how each property corresponds to a field in the table
        $book->title = 'Harry Potter and the Sorcerer\'s Stone';
        $book->author = 'J.K. Rowling';
        $book->published_year = 1997;
        $book->cover_url = 'http://prodimage.images-bn.com/pimages/9780590353427_p0_v1_s484x700.jpg';
        $book->purchase_url = 'http://www.barnesandnoble.com/w/harry-potter-and-the-sorcerers-stone-j-k-rowling/1100036321?ean=9780590353427';

        # Invoke the Eloquent `save` method to generate a new row in the
        # `books` table, with the above data
        $book->save();
        
        # Eloquent get method:
        
        if ($books->isEmpty()) {
            dump('No matches found');
        } else {
            foreach ($books as $book) {
                dump($book->title);
            }
        }
        
        dump('Added: '.$book->title);
    }
    
    public function practice6()
    {
        #Retrieve the last 2 books that were added to the books table.
        $results = Book::orderBy('id', 'desc')->limit(2)->get();
        dump($results->toArray());
    }
    
    public function practice7()
    {
        #Retrieve all the books published after 1950.
        $results = Book::where('published_year', '>', '1950')->get();
        dump($results->toArray());
    }
    
    public function practice8()
    {
        #Retrieve all the books in alphabetical order by title
        $results = Book::orderBy('title')->get();
        dump($results->toArray());
    }
    
    public function practice9()
    {
        #Retrieve all the books in descending order according to published date
        $results = Book::orderBy('published_year', 'desc')->get();
        dump($results->toArray());
    }
        
    public function practice10()
    {
        #Find any books by the author “J.K. Rowling” and update the author name to be “JK Rowling”
        
        $results = Book::where('author', '=', 'J.K. Rowling')->update(['author' => 'JK Rowling']);
        dump($results);
        $results->save();
    }
    
    public function practice11()
    {
        #Remove any/all books with an author name that includes the string “Rowling”.
        $results = Book::where('author', '=', 'J.K. Rowling')->get();
        $results->delete();
    }
    public function practice3()
    {
        $translator = new Parser();
        $translation = $translator->translate('Hello World');
        dump($translation);
    }
    /**
     *
     */
    public function practice2()
    {
        return 'Need help? Email us at '.config('mail.supportEmail');
    }
    /**
     * Demonstrating the first practice example
     */
    public function practice1()
    {
        dump('This is the first example.');
    }
    /**
     * ANY (GET/POST/PUT/DELETE)
     * /practice/{n?}
     * This method accepts all requests to /practice/ and
     * invokes the appropriate method.
     * http://foobooks.loc/practice => Shows a listing of all practice routes
     * http://foobooks.loc/practice/1 => Invokes practice1
     * http://foobooks.loc/practice/5 => Invokes practice5
     * http://foobooks.loc/practice/999 => 404 not found
     */
    public function index($n = null)
    {
        $methods = [];
        # Load the requested `practiceN` method
        if (!is_null($n)) {
            $method = 'practice' . $n; # practice1
            # Invoke the requested method if it exists; if not, throw a 404 error
            return (method_exists($this, $method)) ? $this->$method() : abort(404);
        } # If no `n` is specified, show index of all available methods
        else {
            # Build an array of all methods in this class that start with `practice`
            foreach (get_class_methods($this) as $method) {
                if (strstr($method, 'practice')) {
                    $methods[] = $method;
                }
            }
            # Load the view and pass it the array of methods
            return view('practice')->with(['methods' => $methods]);
        }
    }
}