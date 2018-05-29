<?php
/*
* Plugin Name: Library Books by Manish
* Version: 1.0
* Author: Manish Soni
*/


class Custom_Posts {
     function __construct() {
          register_activation_hook(__FILE__,array($this,'activate'));
		  add_action( 'init', array( $this, 'create_taxonomies' ) );
		  add_action( 'admin_init', array( $this, 'my_admin' ) );
		  add_action( 'admin_menu', array( $this, 'register_book_menu_page' ) );
     } 
     function activate() {
		$this->create_taxonomies();				
		$this->my_admin();
     }
     function create_taxonomies() {
		 $labels = array(
			'name'                  => _x( 'Books', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Book', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Books', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Book', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Add New', 'textdomain' ),
			'add_new_item'          => __( 'Add New Book', 'textdomain' ),
			'new_item'              => __( 'New Book', 'textdomain' ),
			'edit_item'             => __( 'Edit Book', 'textdomain' ),
			'view_item'             => __( 'View Book', 'textdomain' ),
			'all_items'             => __( 'All Books', 'textdomain' ),
			'search_items'          => __( 'Search Books', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent Books:', 'textdomain' ),
			'not_found'             => __( 'No books found.', 'textdomain' ),
			'not_found_in_trash'    => __( 'No books found in Trash.', 'textdomain' ),
			'featured_image'        => _x( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type.', 'textdomain' )
	    );
 
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'book' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		);
		 
		register_post_type( 'book', $args );
	
        $genre_args = array( 
            'hierarchical' => true,  
            'labels' => array(
            	'name'=> _x('Authors', 'taxonomy general name' ),
	            'singular_name' => _x('Author', 'taxonomy singular name'),
    	        'search_items' => __('Search Authors'),
        	    'popular_items' => __('Popular Authors'),
            	'all_items' => __('All Authors'),
            	'edit_item' => __('Edit Author'),
	            'edit_item' => __('Edit Author'),
    	        'update_item' => __('Update Author'),
        	    'add_new_item' => __('Add New Author'),
            	'new_item_name' => __('New Author Name'),
	            'separate_items_with_commas' => __('Seperate Authors with Commas'),
    	        'add_or_remove_items' => __('Add or Remove Authors'),
        	    'choose_from_most_used' => __('Choose from Most Used Authors')
            ),  
            'query_var' => true,  
            'rewrite' => array('slug' =>'authors')        
       );
       
	   register_taxonomy('authors', 'book', $genre_args);
		   	
		$genreargs = array( 
            'hierarchical' => true,  
            'labels' => array(
            	'name'=> _x('Publishers', 'taxonomy general name' ),
	            'singular_name' => _x('Publisher', 'taxonomy singular name'),
    	        'search_items' => __('Search Publishers'),
        	    'popular_items' => __('Popular Publishers'),
            	'all_items' => __('All Publishers'),
            	'edit_item' => __('Edit Publisher'),
	            'edit_item' => __('Edit Publisher'),
    	        'update_item' => __('Update Publisher'),
        	    'add_new_item' => __('Add New Publisher'),
            	'new_item_name' => __('New Publisher Name'),
	            'separate_items_with_commas' => __('Seperate Publishers with Commas'),
    	        'add_or_remove_items' => __('Add or Remove Publishers'),
        	    'choose_from_most_used' => __('Choose from Most Used Publishers')
            ),  
            'query_var' => true,  
            'rewrite' => array('slug' =>'publisher')        
        );
           
		register_taxonomy('publisher', 'book', $genreargs);
		
    }
	 
	 function my_admin() {
		add_meta_box( 'book_meta_box',
			'Books Details',
			array( $this, 'display_books_meta_box' ),
			'book', 'normal', 'high'
		);
	}
	
	function register_book_menu_page() {
		add_submenu_page( 'edit.php?post_type=book', 'Book Shortcode', 'Book Shortcode', 'manage_options', 'book-shortcode', array( $this, 'book_shortcode_main' ) );
	}
	
	function book_shortcode_main() 
	{ 
		echo '<h3>Copy below shortcode and paste into post or page to get the book search options. </h3>';
		echo '<h2><strong>[books_filter]</strong></h2>'; 
	}
	
	function display_books_meta_box( $books_details ) {
		$book_price = esc_html( get_post_meta( $books_details->ID, 'book_price', true ) );
		$book_rating = intval( get_post_meta( $books_details->ID, 'book_rating', true ) );
		?>
		<table>
			<tr>
				<td style="width: 100%">Book Price</td>
				<td><input type="text" size="80" name="book_price" value="<?php echo $book_price; ?>" /></td>
			</tr>
			<tr>
				<td style="width: 150px">Book Rating</td>
				<td>
					<select style="width: 100px" name="book_rating">
					<?php
					// Generate all items of drop-down list
					for ( $rating = 1; $rating <= 5; $rating++ ) {
					?>
						<option value="<?php echo $rating; ?>" <?php echo selected( $rating, $book_rating ); ?>>
						<?php echo $rating; ?> stars <?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}
}

$var = new Custom_Posts();


add_action( 'save_post', 'add_books_fields', 10, 2 );
function add_books_fields( $book_id, $books_details ) {
    // Check post type for books details
    if ( $books_details->post_type == 'book' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['book_price'] ) && $_POST['book_price'] != '' ) {
            update_post_meta( $book_id, 'book_price', $_POST['book_price'] );
        }
        if ( isset( $_POST['book_rating'] ) && $_POST['book_rating'] != '' ) {
            update_post_meta( $book_id, 'book_rating', $_POST['book_rating'] );
        }
    }
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');

function enqueue_style(){
	wp_enqueue_style( 'slider-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
	wp_enqueue_style( 'star-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'custom-css', plugins_url( '/css/custom.css', __FILE__ ));

   	wp_enqueue_script( 'default-jquery', 'https://code.jquery.com/jquery-1.12.4.js' ); 
	wp_enqueue_script( 'slider-jquery', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js' ); 	
	wp_enqueue_script( 'custom-js', plugins_url( '/js/custom.js', __FILE__ )); 
	
	wp_localize_script( 'custom-js', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 	
}

function books_filter_function( $atts ) {

	global $wpdb;
	$publishers = get_terms('publisher', array('hide_empty' => false));
	$max_price = $wpdb->get_results("SELECT max(meta_value) as max_price FROM wp_postmeta WHERE meta_key='book_price'");
	
	$content = '<div class="filters">
		<input type="hidden" id="max_price" value="'.$max_price[0]->max_price.'" />
		<h3 style="text-align:center;">Book Search</h3>
		<div style="display:block;">
			<label style="display:inline-block; width:45%; margin-right:15px;"> Book name: <input type="text" name="book_name" id="book_name" /></label>
			<label style="display:inline-block; width:45%;"> Author: <input type="text" name="author" id="author" /></label>
		</div>
		<br/>
		<div style="display:block;">
			<label style="display:inline-block;width:45%; margin-right:15px;"> Publisher:<br/>
				<select name="publisher" id="publisher" style="width:100%;">
					<option value="0">Please Select</option>';
					foreach($publishers as $publisher) {
						$content .= '<option value="'.$publisher->term_id.'">'.$publisher->name.'</option>';
					}
				$content .= '</select>
			</label>
			<label style="display:inline-block;width:45%;"> Rating:<br/>
				<select name="rating" id="rating" style="width:100%;">
					<option value="">Select Rating</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>				
				</select>
			</label>
		</div>
		<br/>
		<div style="position:relative;">
  			<label for="amount">Price:</label>
  			<input type="text" id="amount_start" readonly />
			<input type="text" id="amount_end" readonly />
			<div id="slider"></div>
		</div>		
		<br/>
		<input type="button" id="search_books" value="Search" />
	</div>
	
	<div id="books_list"></div>
	';
	return $content;
}
add_shortcode( 'books_filter', 'books_filter_function' );

function get_book_list() {

	global $post;	

	ob_start();
	include 'ajax_data.php';
	$content = ob_get_contents();
	ob_end_clean ();

	echo $content;

	die;
}
add_action( 'wp_ajax_get_book_list', 'get_book_list' );
add_action( 'wp_ajax_nopriv_get_book_list', 'get_book_list' );

add_filter('single_template', 'my_custom_template');
function my_custom_template($single) {

    global $wp_query, $post;
	
    if ( $post->post_type == 'book' ) {
		return dirname( __FILE__ ) . '/custom_post.php';
    }

    return $single;

}


function kriesi_pagination($pages = '', $range = 2)
{  
    $showitems = ($range * 2)+1;  

	global $paged;
	if(empty($paged)) $paged = 1;

	if($pages == '')
	{
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if(!$pages)
		{
			$pages = 1;
		}
	}

	if(1 != $pages)
	{
		echo "<div class='pagination'>";
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a data-pageno='1' href='javascript:void(0);'>&laquo;</a>";
		if($paged > 1 && $showitems < $pages) echo "<a data-pageno='".($paged - 1)."' href='javascript:void(0);'>&lsaquo;</a>";

		for ($i=1; $i <= $pages; $i++)
		{
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
			{
				echo ($paged == $i)? "<a>".$i."</a>":"<a data-pageno='".$i."' href='javascript:void(0);' class='inactive' >".$i."</a>";
			}
		}

		if ($paged < $pages && $showitems < $pages) echo "<a data-pageno='".($paged + 1)."' href='javascript:void(0);'>&rsaquo;</a>";  
		if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a data-pageno='".pages."' href='javascript:void(0);'>&raquo;</a>";
		echo "</div>\n";
	}
}
?>