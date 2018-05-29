<?php
$book_name = $_POST['book_name'];
$author = $_POST['author'];
$publisher = $_POST['publisher'];
$rating = $_POST['rating'];
$price_start = $_POST['price_start'];
$price_end = $_POST['price_end'];
$page = sanitize_text_field($_POST['pagination']);
$page == '' ? $page = 0 : $page -= 1;
$per_page = 3;
$start = $page * $per_page;

$tax_query = array(); 
if($author) {
	$author_array = array(
		'taxonomy' => 'authors',
		'field'    => 'slug',
		'terms'    => $author,
	);
	$tax_query[] = $author_array;
}
if($publisher) {
	$publisher_array = array(
		'taxonomy' => 'publisher',
		'field'    => 'term_id',
		'terms'    => $publisher,
	);
	$tax_query[] = $publisher_array;
}
if(!empty($tax_query)) {
	$tax_query['relation'] = 'AND';
}

$meta_query = array('relation' => 'AND');
$price_array = array(
	'key'     => 'book_price',
	'value'   => array( $price_start, $price_end ),
	'type'    => 'numeric',
	'compare' => 'BETWEEN',
);
$meta_query[] = $price_array;

if($rating) {
	$rating_array = array(
		'key'     => 'book_rating',
		'value'   => $rating,					
		'compare' => '=',
	);
	$meta_query[] = $rating_array;
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$args = array(
	'post_type' => 'book',
	'posts_per_page' => $per_page,
	'orderby' => 'ID',
	'order' => 'DESC',
	'paged' => $paged,
	'offset' => $start,
);

$book_name != '' ? $args['name'] = $book_name : '';

$args['meta_query'] = $meta_query;
!empty($tax_query) ? $args['tax_query'] = $tax_query : '';

$the_query = new WP_Query( $args ); ?>

<?php if ( $the_query->have_posts() ) : ?>
	
	<table>
		<thead><tr>
			<th>Book Title</th>
            <th>Price</th>
			<th>Author</th>
			<th>Publisher</th>
			<th>Rating</th>
		</tr></thead>
		<tbody>			
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<?php 				
				$authors = get_the_terms($post->ID, 'authors');
				$publishers = get_the_terms($post->ID, 'publisher');
				$rating = get_post_meta($post->ID, 'book_rating', true);
				$price = get_post_meta($post->ID, 'book_price', true);	
				
				$author_arr = array();
				foreach($authors as $author) {
					$author_arr[] = $author->name;
				}
				$author_arr = implode(", ", $author_arr);
				
				$publisher_arr = array();
				foreach($publishers as $publisher) {
					$publisher_arr[] = $publisher->name;
				}
				$publisher_arr = implode(", ", $publisher_arr);
				?>
				<tr>
					<td><a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></td>
					<td><?php echo '$'.$price; ?></td>
                    <td><?php echo $author_arr; ?></td>
					<td><?php echo $publisher_arr; ?></td>
					<td>
						<?php for($i=1;$i<=5;$i++) { ?>
							<span class="fa fa-star <?php echo $i <= $rating ? 'checked' : '' ?>"></span>
						<?php } ?>						
					</td>
				</tr>
			<?php endwhile; ?>			
		</tbody>
	</table>
	
	<?php echo kriesi_pagination($the_query->max_num_pages); ?>	

	<?php wp_reset_postdata(); ?>

<?php else : 
	echo 'error';
endif; ?>