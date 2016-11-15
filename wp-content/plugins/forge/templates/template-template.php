<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>"/>
		<title>
			<?php wp_title(''); ?>
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<?php do_action('genesis_meta'); ?>
		<?php wp_head(); ?>
	</head>
	<?php remove_all_filters('body_class'); ?>
	<body <?php body_class(); ?> style="background:#fff; padding:40px;">
		<?php if(have_posts()) while(have_posts()): the_post(); ?>
		<?php the_content(); ?>
		<?php endwhile; ?>
		<?php wp_footer(); ?>
	</body>
</html>