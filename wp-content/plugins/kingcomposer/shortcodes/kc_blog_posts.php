<?php
$items = $number_item = $words = $show_date = $custom_class = $data_owl = '';
$layout = $i = 1;

$atts['post_type']	= 'post';
$atts['taxonomy']	= 'category';
$list_posts			= kc_tools::get_posts( $atts );
$css_class			= apply_filters( 'kc-el-class', $atts );

extract( $atts );

$css_class[] = 'kc-blog-posts kc-blog-posts-' . $layout;
if ( !empty( $custom_class ) )
	$css_class[] = $custom_class;

switch ( $layout ) {
	case '1':
		$css_class[] 	= 'owl-carousel';
		$data_owl 		= ' data-owl-options=\'{"autoplay": "yes", "pagination": "yes", "items": "1", "tablet":1, "mobile":1}\'';
	break;
	case '3':
		$css_class[] = 'kc-blog-grid kc_blog_masonry';
	break;
	default:

	break;
}

?>

<div class="<?php echo esc_attr( implode( ' ', $css_class ) ) ;?>"<?php echo $data_owl; ?>>
	<?php if( count( $list_posts ) ): ?>

		<?php switch ( $layout ) {
			case '2':
				foreach( $list_posts as $item ) :
					$img 		= kc_tools:: get_featured_image( $item );
					$img_url 	= kc_tools::createImageSize( $img, '543x304xct' );
		?>

					<div class="kc-list-item-2">

						<?php if ( $i%2 == 1 ): ?>

							<div class="post-item-left">
								<figure>
									<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo get_the_title( $item ); ?>">
								</figure>
							</div>
							<div class="post-item-right">
								<div class="post_details">
									<h2 class="post-title-alt"><a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" title="<?php echo get_the_title( $item ); ?>"><?php echo get_the_title( $item ); ?></a></h2>

									<?php if ( $show_date == 'yes' ): ?>
										<div class="post-meta">
											<span class="post-author"><i class="fa fa-user"></i>  <a href="<?php echo get_author_posts_url( $item->post_author ); ?>" title="<?php esc_html_e( 'Posts by ', 'kingcomposer' ); echo get_the_author_meta( 'display_name', $item->post_author ); ?>" rel="author"><?php echo get_the_author_meta( 'display_name', $item->post_author ); ?></a></span>
											<span class="post-date"><i class="fa fa-clock-o"></i> <a href="<?php echo get_month_link( get_the_time( 'Y', $item->ID ), get_the_time( 'm', $item->ID ) ); ?>"><?php echo get_the_date( 'F m Y', $item->ID ); ?></a> </span>
											<?php if ( get_the_category( $item->ID ) ): ?>
												<span class="post-cats"><i class="fa fa-folder-o" aria-hidden="true"></i> <?php the_category( ', ', '', $item->ID ); ?></span>
											<?php endif ?>
										</div>
									<?php endif ?>
								</div>
								<?php if ( $words > 0 ): ?>
									<p><?php echo wp_trim_words( $item->post_content, $words ); ?></p>
								<?php endif ?>
								<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="post-2-button"><?php esc_html_e( 'Read more', 'kingcomposer' ); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
							</div>

						<?php else: ?>

							<div class="post-item-left">
								<div class="post_details">
									<h2 class="post-title-alt"><a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" title="<?php echo get_the_title( $item ); ?>"><?php echo get_the_title( $item ); ?></a></h2>

									<?php if ( $show_date == 'yes' ): ?>
										<div class="post-meta">
											<span class="post-author"><i class="fa fa-user"></i>  <a href="<?php echo get_author_posts_url( $item->post_author ); ?>" title="<?php esc_html_e( 'Posts by ', 'kingcomposer' ); echo get_the_author_meta( 'display_name', $item->post_author ); ?>" rel="author"><?php echo get_the_author_meta( 'display_name', $item->post_author ); ?></a></span>
											<span class="post-date"><i class="fa fa-clock-o"></i> <a href="<?php echo get_month_link( get_the_time( 'Y', $item->ID ), get_the_time( 'm', $item->ID ) ); ?>"><?php echo get_the_date( 'F m Y', $item->ID ); ?></a> </span>
											<?php if ( get_the_category( $item->ID ) ): ?>
												<span class="post-cats"><i class="fa fa-folder-o" aria-hidden="true"></i> <?php the_category( ', ', '', $item->ID ); ?></span>
											<?php endif ?>
										</div>
									<?php endif ?>
								</div>
								<?php if ( $words > 0 ): ?>
									<p><?php echo wp_trim_words( $item->post_content, $words ); ?></p>
								<?php endif ?>
								<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="post-2-button"><?php esc_html_e( 'Read more', 'kingcomposer' ); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
							</div>
							<div class="post-item-right">
								<figure>
									<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo get_the_title( $item ); ?>">
								</figure>
							</div>

						<?php endif ?>

					</div>

		<?php
					$i++;
				endforeach;
			break;
			case '3':
				kc_js_callback('kc_front.blog.masonry');
				foreach( $list_posts as $item ) :
					$img = kc_tools:: get_featured_image( $item );
		?>

					<div class="post-grid grid-<?php echo $number_item; ?>">
						<div class="kc-list-item-3">
							<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="entry-thumb-link">
								<div class="entry-thumb-wrapper">
									<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo get_the_title( $item ); ?>"/>
									<div class="entry-thumb-overlay"></div>
								</div>
							</a>
							<?php if ( $show_date == 'yes' ): ?>
								<div class="entry-meta">
									<span class="entry-date"><a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>"><?php echo get_the_date( 'F m, Y', $item->ID ); ?></a></span>
									<?php if ( get_the_category( $item->ID ) ): ?>
										<span class="entry-cats"><?php the_category( ', ', '', $item->ID ); ?></span>
									<?php endif ?>
								</div>
							<?php endif ?>
							<h2 class="post-title-alt"><a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>"><?php echo get_the_title( $item ); ?></a></h2>
							<?php if ( $words > 0 ): ?>
								<div class="entry-excerpt">
									<p><?php echo wp_trim_words( $item->post_content, $words ); ?></p>
								</div>
							<?php endif ?>
						</div>
					</div>

		<?php
				endforeach;
			break;
			case '4':
				foreach( $list_posts as $item ) :
					$img 		= kc_tools:: get_featured_image( $item );
					$img_url 	= kc_tools::createImageSize( $img, '370x250xct' );
		?>

					<div class="grid-<?php echo esc_attr( $number_item ); ?>">
						<div class="kc-list-item-4">
							<div class="kc-post-header">
								<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>">
									<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo get_the_title( $item ); ?>">
								</a>

								<div class="meta-title">
									<?php if ( $show_date == 'yes' ): ?>
										<div class="post-meta">
											<?php if ( get_the_category( $item->ID ) ): ?>
												<?php the_category( ', ', '', $item->ID ); ?>
											<?php endif ?>
											<a href="<?php echo get_month_link( get_the_time( 'Y', $item->ID ), get_the_time( 'm', $item->ID ) ); ?>" class="date-link"><?php echo get_the_date( 'd.F.Y', $item->ID ); ?></a>
										</div>
									<?php endif ?>

									<h2 class="post-title-alt">
										<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="post-title-link" title="<?php echo get_the_title( $item ); ?>"><?php echo get_the_title( $item ); ?></a>
									</h2>
								</div>
							</div>

							<?php if ( $words > 0 ): ?>
								<div class="post-content"><p><?php echo wp_trim_words( $item->post_content, $words ); ?></p></div>
							<?php endif ?>

							<div class="post-footer">
								<ul class="social-share">
									<li>
										<a href="<?php echo get_comments_link( $item->ID ) ?>" class="comment-count fa fa-comments" data-id="32" title=""><span class="number"> <?php echo get_comments_number( $item->ID ); ?></span></a>
									</li>
									<li>
										<a href="http://www.facebook.com/sharer.php?u=<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="fa fa-facebook" target="_blank" title="<?php esc_html_e( 'Share on Facebook', 'kingcomposer' ); ?>"></a>
									</li>
									<li>
										<a href="http://twitter.com/home?status=<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="fa fa-twitter" target="_blank" title="<?php esc_html_e( 'Share on Twitter', 'kingcomposer' ); ?>"></a>
									</li>
									<li>
										<a href="http://plus.google.com/share?url=<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="fa fa-google-plus" target="_blank" title="<?php esc_html_e( 'Share on Google+', 'kingcomposer' ); ?>"></a>
									</li>
									<li>
										<a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( get_permalink( $item->ID ) ); ?>&amp;media=<?php echo esc_url( $img_url ); ?>" class="fa fa-pinterest-p" target="_blank" title="<?php esc_html_e( 'Share on Pinterest', 'kingcomposer' ); ?>"></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
		<?php
				endforeach;
			break;
			default:
				foreach( $list_posts as $item ) :
					$img 		= kc_tools:: get_featured_image( $item );
					$img_url	= kc_tools::createImageSize( $img, '1140x550xct' );
		?>

					<div class="item kc-list-item-1">
						<figure>
							<img src="<?php echo esc_url( $img_url ); ?>" alt="">
						</figure>

						<div class="post-details">
							<h2 class="post-title-alt">
								<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" title="<?php echo get_the_title( $item ); ?>"><?php echo get_the_title( $item ); ?></a>
							</h2>

							<?php if ( $show_date == 'yes' ): ?>
								<div class="post-date">
									<span class="post-author"><?php esc_html_e( 'by', 'kingcomposer' ); ?> <a href="<?php echo get_author_posts_url( $item->post_author ); ?>" title="<?php esc_html_e( 'Posts by ', 'kingcomposer' ); echo get_the_author_meta( 'display_name', $item->post_author ); ?>" rel="author"><?php echo get_the_author_meta( 'display_name', $item->post_author ); ?></a></span>
									<?php echo get_the_date( 'F m Y', $item->ID ); ?>
									<?php if ( get_the_category( $item->ID ) ): ?>
										<span class="post-cats"><?php the_category( ', ', '', $item->ID ); ?></span>
									<?php endif ?>
								</div>
							<?php endif ?>
						</div>
					</div>

		<?php
				endforeach;
			break;
		} ?>

	<?php else: ?>

		<h3><?php echo __( 'Blog Post: Nothing not found.', 'kingcomposer' ); ?></h3>

	<?php endif ?>

</div>