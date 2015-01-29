<?php get_header(); ?>

<section id="omc-main">	
	
	<div class="omc-cat-top"><h1><?php _e('有', 'gonzo');?> <em>"<?php printf(single_tag_title( '', false ));?>"</em> <?php _e('標籤的文章', 'gonzo');?> </h1></div>	

	<?php get_template_part('loop', 'blog-style-2'); ?>
	
</section><!-- /omc-main -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
