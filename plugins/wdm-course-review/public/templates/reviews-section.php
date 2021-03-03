<?php
/**
 * This file is the template for showing reviews on the course single page.
 *
 * @package RatingsReviewsFeedback\Public\Reviews
 */

global $post;
if ( empty( $course_id ) ) {
	$course_id = $post->ID;
}
$course_ratings = rrf_get_course_rating_details( $course_id );
$review_split = rrf_get_bar_values( $course_ratings );
$is_review_comments_enabled = get_option( 'wdm_course_review_setting', 1 );
$rating_args = array(
	'size'          => 'xs',
	'show-clear'    => false,
	'show-caption'  => false,
	'readonly'      => true,
);
$can_submit_rating = false;
$review_prompt_text = sprintf( '<span>%s</span>', __( 'What\'s your experience? We\'d love to know!', 'wdm_ld_course_review' ) );
$class              = '';
if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$can_submit_rating  = rrf_can_user_post_reviews( $user_id, $course_id );
	$show_submission        = false;
	if ( $can_submit_rating ) {
		$show_submission        = true;
		$user_ratings   = rrf_get_user_course_review_id( $user_id, $course_id );
		if ( empty( $user_ratings ) ) {
			$course_ratings['user_rating']  = 0.0;
			$review_btn_text                    = __( 'Write a Review', 'wdm_ld_course_review' );
			$class                          = 'not-rated';
		} else {
			$course_ratings['user_rating']  = intval( get_post_meta( $user_ratings->ID, 'wdm_course_review_review_rating', true ) );
			$review_btn_text                    = __( 'Edit your Review', 'wdm_ld_course_review' );
			$class                          = 'already-rated';
			$draft_additional_message = '';
			if ( 'pending' == $user_ratings->post_status ) {
				$draft_additional_message = __( 'Currently we are processing your review.', 'wdm_ld_course_review' );
			}
			$review_prompt_text = sprintf( '<span>%s</span><br/><span><small>%s</small></span>%s', __( 'Your Rating', 'wdm_ld_course_review' ), $draft_additional_message, rrf_get_star_html_struct( $course_id, $course_ratings['user_rating'], $rating_args ) );
		}
	} else {
		$class              = 'not-allowed';
	}
}
?>
<div id="course-reviews-section" class="course-reviews-section <?php echo esc_attr( $class ); ?>">
	<h3><?php esc_html_e( 'Ratings and Reviews', 'wdm_ld_course_review' ); ?></h3>
	<div class="review-top-section">
		<div class="review-top-col">
			<div class="review-top-star-wrap">
				<div class="review-stars-top">
					<?php
					echo rrf_get_star_html_struct( $course_id, $course_ratings['average_rating'], $rating_args );// WPCS: XSS ok.
					?>
				</div><!-- .review-stars-top closing -->
				<div class="reviews-avg">
					<?php echo number_format( $course_ratings['average_rating'], 1 ); ?>
				</div>
				<div class="reviews-avg-label">
					<?php esc_html_e( 'Avg. Rating', 'wdm_ld_course_review' ); ?>
				</div>
			</div><!-- .review-top-star-wrap closing -->
			<div class="reviews-total-wrap">
				<?php
				/* translators: %d : Total Number of Reviews. */
				echo sprintf( __( '<span class="reviews-total">%d</span> Ratings', 'wdm_ld_course_review' ), $course_ratings['total_count'] );// WPCS: XSS ok.
				?>
			</div><!-- .reviews-total-wrap closing -->
		</div><!-- first .review-top-col closing -->
		<div class="review-top-col">
			<?php for ( $i = count( $review_split ); $i > 0; $i-- ) : ?>
			<div class="review-split-wrap">
				<div class="review-split-title"><?php echo esc_html( $i ); ?></div>
				<div class="review-split-percent">
						<div class="review-split-percent-inner review-split-percent-inner-1"></div>
						<div class="review-split-percent-inner review-split-percent-inner-2" style="width:<?php echo esc_attr( $review_split[ $i ]['percentage'] ); ?>%;"></div>
				</div><!-- .review-split-percent closing -->
				<div class="review-split-count"><?php echo esc_html( $review_split[ $i ]['value'] ); ?></div>
			</div><!-- .review-split-wrap closing -->
			<?php endfor; ?>
		</div><!-- second .review-top-col closing -->
		<?php
		if ( ! is_user_logged_in() ) {
			?>
			<div class="review-top-col">
				<div class="review-top-desc">
					<?php
					echo $review_prompt_text; // WPCS : XSS ok.
					?>
				</div>
				<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="login-to-enroll button">
						<?php echo esc_html__( 'Login to Review', 'wdm_ld_course_review' ); ?>
				</a>
			</div><!-- third .review-top-col closing -->
			<?php
		}
		if ( $can_submit_rating ) {
			?>
			<div class="review-top-col">
				<div class="review-top-desc">
					<?php
					echo $review_prompt_text; // WPCS : XSS ok.
					?>
				</div>
				<button class="write-a-review <?php echo esc_attr( $class ); ?>" data-course_id="<?php echo esc_attr( $course_id ); ?>">
					<?php echo esc_html( $review_btn_text ); ?>
				</button>
			</div><!-- third .review-top-col closing -->
				<?php
		}
		?>
	</div><!-- .review-top-section closing -->
	<div class="filter-options">
		<div class="select">
			<select class="select-text sort_results" required>
				<option value="date" selected><?php esc_html_e( 'Most Recent', 'wdm_ld_course_review' ); ?></option>
				<option value="meta_value_num"><?php esc_html_e( 'Top Ratings', 'wdm_ld_course_review' ); ?></option>
			</select>
			<span class="select-highlight"></span>
			<span class="select-bar"></span>
			<label class="select-label"><?php esc_html_e( 'Sort by', 'wdm_ld_course_review' ); ?></label>
		</div> <!-- first .select closing -->
		<div class="select">
			<select class="select-text filter_results" required>
				<option value="-1" selected><?php esc_html_e( 'All Stars', 'wdm_ld_course_review' ); ?></option>
				<option value="5"><?php esc_html_e( '5 star only', 'wdm_ld_course_review' ); ?></option>
				<option value="4"><?php esc_html_e( '4 star only', 'wdm_ld_course_review' ); ?></option>
				<option value="3"><?php esc_html_e( '3 star only', 'wdm_ld_course_review' ); ?></option>
				<option value="2"><?php esc_html_e( '2 star only', 'wdm_ld_course_review' ); ?></option>
				<option value="1"><?php esc_html_e( '1 star only', 'wdm_ld_course_review' ); ?></option>
			</select>
			<span class="select-highlight"></span>
			<span class="select-bar"></span>
			<label class="select-label"><?php esc_html_e( 'Filter by', 'wdm_ld_course_review' ); ?></label>
		</div> <!-- second .select closing. -->
	</div> <!-- .filter-options closing -->
	<div class="loader hide"><img src="<?php echo esc_url( RRF_PLUGIN_URL . '/public/images/loader.gif' ); ?>"></div>
	<div class="reviews-listing-wrap" id="reviews-listing-wrap">
		<?php include \ns_wdm_ld_course_review\Review_Submission::get_template( 'reviews-listing.php' ); ?>
	</div><!-- .reviews-listing-wrap closing. -->
	<?php
	if ( ! is_user_logged_in() ) {
		?>
			<div class="write-review-wrap">
				<div class="review-top-desc">
					<?php
					echo $review_prompt_text; // WPCS : XSS ok.
					?>
				</div>
				<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="login-to-enroll button">
					<?php echo esc_html__( 'Login to Review', 'wdm_ld_course_review' ); ?>
				</a>
			</div><!-- .write-review-wrap closing -->
		<?php
	}
	if ( $can_submit_rating ) {
		?>
			<div class="write-review-wrap">
				<div class="review-top-desc">
					<?php
					echo $review_prompt_text; // WPCS : XSS ok.
					?>
				</div>
				<button class="write-a-review <?php echo esc_attr( $class ); ?>" data-course_id="<?php echo esc_attr( $course_id ); ?>">
					<?php echo esc_html( $review_btn_text ); ?>
				</button>
			</div><!-- .write-review-wrap closing -->
			<?php
	}
	?>
</div> <!-- .course-reviews-section closing -->
