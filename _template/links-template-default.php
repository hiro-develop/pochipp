<?php
	$posts = get_posts( $arg );
	$target = $this->get_rel_target_text();
	if ($posts) {?>
		<ul class="<?php echo esc_attr( $this->add_prefix('itemlinks') ) ?> tag_id_<?php echo esc_attr( $atts[ 'tag_id' ] ) ?>">
			<?php foreach ( $posts as $post ) {
				$data = (array)$post;
				$data[ 'post_id' ]		= $data[ 'ID'];
				$data[ 'title' ]		= $data[ 'post_title' ];
				$meta_data = $this->upate_html_data( $data, $att );
				$seach_shop_value = get_post_meta( $post->ID, $this->custom_field_column_name( 'search_shop_value' ), true );
				$is_rakuten = $this->is_search_from_rakuten( $seach_shop_value );
				if ( $is_rakuten ) {
					$original_url =  get_post_meta( $post->ID, $this->custom_field_column_name( 'rakuten_title_url' ), true );
					$url = $this->generate_rakuten_title_link_with_aid( $original_url, $post->ID );
				} else {
					$original_url =  get_post_meta( $post->ID, $this->custom_field_column_name( 'amazon_title_url' ), true );
					$url = $this->generate_amazon_title_link_with_aid( $original_url, $post->ID );
				}

				if ( strlen( $url ) > 0 ) {
				?>
				<li><a href="<?php echo esc_url( $url ) ?>"<?php echo $target ?>><?php echo esc_html( $post->post_title ) ?></a></li>
			<?php }
			} ?>
		</ul>
		<?php
		wp_reset_postdata();
	}
