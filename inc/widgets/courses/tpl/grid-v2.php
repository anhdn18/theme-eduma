<?php
global $post;

$limit             = $instance['limit'];
$columns           = $instance['grid-options']['columns'];
$view_all_course   = ( $instance['view_all_courses'] && '' != $instance['view_all_courses'] ) ? $instance['view_all_courses'] : false;
$view_all_position = ( $instance['view_all_position'] && '' != $instance['view_all_position'] ) ? $instance['view_all_position'] : 'top';
$sort              = $instance['order'];
$feature        = !empty( $instance['featured'] ) ? true : false ;

$condition = array(
	'post_type'           => 'lp_course',
	'posts_per_page'      => $limit,
	'ignore_sticky_posts' => true,
);

if ( $sort == 'category' && $instance['cat_id'] && $instance['cat_id'] != 'all' ) {
	if ( get_term( $instance['cat_id'], 'course_category' ) ) {
		$condition['tax_query'] = array(
			array(
				'taxonomy' => 'course_category',
				'field'    => 'term_id',
				'terms'    => $instance['cat_id']
			),
		);
	}
}

if ( $sort == 'popular' ) {
	$post_in = eduma_lp_get_popular_courses( $limit );

	$condition['post__in'] = $post_in;
	$condition['orderby']  = 'post__in';
}

if( $feature ) {
	$condition['meta_query'] = array(
		array(
			'key' => '_lp_featured',
			'value' =>  'yes',
		)
	);
}

$the_query = new WP_Query( $condition );

if ( $the_query->have_posts() ) :
	if ( $instance['title'] ) {
		echo ent2ncr( $args['before_title'] . $instance['title'] . $args['after_title'] );
	}
	if ( $view_all_course && 'top' == $view_all_position ) {
		echo '<a class="view-all-courses position-top" href="' . get_post_type_archive_link( 'lp_course' ) . '">' . esc_attr( $view_all_course ) . '</a>';
	}
	?>
	<div class="thim-course-grid">
		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
            <?php _learn_press_count_users_enrolled_courses( array( $post->ID ) ); ?>
			<div class="lpr_course <?php echo 'course-grid-' . $columns; ?>">
				<div class="course-item">
					<?php
					echo '<div class="course-thumbnail">';
					echo '<a class="thumb" href="' . esc_url( get_the_permalink() ) . '" >';
					echo thim_get_feature_image( get_post_thumbnail_id( $post->ID ), 'full', apply_filters( 'thim_course_thumbnail_width', 450 ), apply_filters( 'thim_course_thumbnail_height', 450 ), get_the_title() );
					echo '</a>';
					thim_course_wishlist_button();
					echo '<a class="course-readmore" href="' . esc_url( get_the_permalink() ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>';
					echo '</div>';
					?>
					<div class="thim-course-content">
						<?php
						learn_press_course_instructor();
						?>
						<h2 class="course-title">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>"> <?php echo get_the_title(); ?></a>
						</h2>

						<div class="course-meta">
							<?php learn_press_course_students(); ?>
							<?php thim_course_ratings_count(); ?>
							<?php learn_press_courses_loop_item_price(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		endwhile;
		?>
	</div>
	<?php
	if ( $view_all_course && 'bottom' == $view_all_position ) {
		echo '<div class="wrapper-bottom-view-courses"><a class="view-all-courses position-bottom" href="' . get_post_type_archive_link( 'lp_course' ) . '">' . esc_attr( $view_all_course ) . '</a></div>';
	}

endif;

wp_reset_postdata();
