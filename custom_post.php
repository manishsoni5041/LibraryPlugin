<?php
/*
* Custom Post Type Template
*/

get_header();
?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
			$authors = get_the_terms($post->ID, 'authors');
			$publishers = get_the_terms($post->ID, 'publisher');
			?>
			<h2><?php echo get_the_title(); ?></h2>
            <span>Authors: &nbsp; </span>
            <?php
            foreach($authors as $author) { ?>
				<a style="text-decoration: underline;" href="<?php echo get_term_link($author->term_id); ?>"><?php echo $author->name; ?></a> &nbsp; &nbsp;
            <?php } ?>
            <br/>
            <span>Publishers: &nbsp; </span>
            <?php
            foreach($publishers as $publisher) { ?>
				<a style="text-decoration: underline;" href="<?php echo get_term_link($publisher->term_id); ?>"><?php echo $publisher->name; ?></a> &nbsp; &nbsp;
            <?php } ?>
            
            <p>&nbsp;</p>
                         
            <?php the_content(); ?>
            <span>Price: &nbsp; $<?php echo get_post_meta($post->ID, 'book_price', true); ?></span>
			<?php $rating = get_post_meta($post->ID, 'book_rating', true); ?>
            <br/>
            Rating: &nbsp;
            <?php for($i=1;$i<=5;$i++) { ?>
            <span class="fa fa-star <?php echo $i <= $rating ? 'checked' : '' ?>"></span>
            <?php } ?>
			<?php
			endwhile; // End of the loop.
			?>

		</main>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>