<div class="yyi_rinker_info_dashboard">
<?php
$args = array(
	'post_type'			=> self::LINK_POST_TYPE,
	'meta_key'			=>  $this->add_prefix( 'is_amazon_no_exist' ),
	'meta_value'		=> 1,
	'posts_per_page'	=> -1,
);

$the_query = new WP_Query( $args );
if ( $the_query->post_count === 0 ) {?>
	<p>Amazonに新しい情報はありません</p>
<?php
} else { ?>
	<p class="alert">Amazonで取扱いが無くなった商品があります</p>
<?php }
while ( $the_query->have_posts() ) : $the_query->the_post();
	$post		= get_post();
	$post_id	= get_the_ID();;
	$src		= admin_url() . 'post.php?post=' . $post_id  . '&action=edit';
	echo '<a href="' . esc_url( $src ) . '">[' .  esc_html( $post_id )  . ']</a>';
endwhile;
wp_reset_postdata();
?>
	<?php
	$args = array(
		'post_type'			=> self::LINK_POST_TYPE,
		'meta_key'			=>  $this->add_prefix( 'is_rakuten_no_exist' ),
		'meta_value'		=> 1,
		'posts_per_page'	=> -1,
	);

	$the_query = new WP_Query( $args );
	if ( $the_query->post_count === 0 ) {?>
        <p>楽天市場に新しい情報はありません</p>
		<?php
	} else { ?>
        <p class="alert">楽天市場で取扱いが無くなった商品があります</p>
	<?php }
	while ( $the_query->have_posts() ) : $the_query->the_post();
		$post		= get_post();
		$post_id	= get_the_ID();;
		$src		= admin_url() . 'post.php?post=' . $post_id  . '&action=edit';
		echo '<a href="' . esc_url( $src ) . '">[' .  esc_html( $post_id )  . ']</a>';
	endwhile;
	wp_reset_postdata();
	?>
</div>


