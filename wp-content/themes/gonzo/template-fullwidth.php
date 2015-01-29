<?php
/*
Template Name: Full Width Page
*/
?>

<?php get_header(); ?>

<section id="omc-main-full-width" class="omc-full-width">	

	<article id="omc-full-article">
	
		<h1><?php the_title(); ?> </h1>
		
		<!--<?php 	the_post_thumbnail('blog-full-width', array('class' => 'featured-full-width-top')); ?>-->
		
		<?php the_content();?>		

		<div class="fb-comments" data-href="<?php echo get_permalink(); ?>" data-num-posts="5" data-width="100%"></div>


		<div class="omc-page-space"></div>
		
	</article><!-- /omc-full-article -->



</section><!-- /omc-main -->

<br class="clear" />



</div> <!-- end of #container -->


<?php get_footer();?>