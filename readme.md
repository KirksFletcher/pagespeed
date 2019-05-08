
# Laravel Pagespeed

Pagespeed is a simple package to apply filters to the view output such as removing whitespace and comments (a lot more to come). The benefit is quite considerable page speed increase, great if you are looking to increase score on Google's Pagespeed Insights, and even better for a smooth user experience.

In addition to applying selected filters, rendered views are cached using the cache configuration you have set-up in Laravel (I recommend using memcached). This allows for lightning fast rendering and delivery of pages.

Obviously while this is good for static pages such as homepage, contact us etc, this will prevent the use of pages with dynamic content. However, if a user is logged in the package will skip serving the cached page and serve a dynamic page (still applying filters).

### Usage is simple: 

Whilst you are free to choose your own implementation method, here is the one I like to use.


In your main Controller.php instantiate the Pagespeed class, and select required filters.

    <?php  
      
    namespace App\Http\Controllers;  
      
    use Illuminate\Foundation\Bus\DispatchesJobs;  
    use Illuminate\Routing\Controller as BaseController;  
    use Illuminate\Foundation\Validation\ValidatesRequests;  
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  
    use kirksfletcher\pagespeed\Pagespeed;  
      
    class Controller extends BaseController  
    {  
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;  
      
        protected $pagespeed;  
      
        public function __construct()  
        {  
            $this->pagespeed = new Pagespeed();  
            $this->pagespeed->plugin('trimWhiteSpace', true);  
            $this->pagespeed->plugin('removeComments', true);  
        }  
    }


Now in any of your controllers render your views like so.

    <?php  
      
    namespace App\Http\Controllers;  
      
    use Illuminate\Http\Request;  
      
    class test extends Controller  
    {  
      
        public function test(){  
      
            $pageData = [  
                'title' => 'This is my page title'  
		    ];  
      
            return $this->pagespeed->view('welcome', $pageData, '/test');  
        }  
      
    }

The main ->view function accepts 3 arguments:

- The first is required and is the view itself (used in the same way you would call a view in Laravel)
- The second is optional and is the data you wish to send to the view
- The third is also optional and is the page URL (slug), you may have one function render different pages depending on the slug used this allows different caches to be created based on the slug, another use is the ability to md5 the page data here to make sure that pages are cached based on dynamic content (only useful if you have limited variations, if every request is different it may not be so useful). Where a slug has not been passed the view name will be used for cache purposes.

### Other useful commands

    $this->pagespeed->killCacheView(VIEW_NAME_OR_SLUG);

The above command is quite obvious and will clear the cache for the selected processed view.


    $this->pagespeed->plugin('removeComments', true); 

The above command can be called at any time (is usually best to call just after instantiating). This will apply the selected filters whilst building the cached rendered view.

#### Available plugins

 
| Plugin | Description |
|--|--|
| 'removeComments' | Remove all html comments from rendered view |
| 'trimWhiteSpace' | Remove all whitespace from html (minification) |

(many more plugins being developed)

