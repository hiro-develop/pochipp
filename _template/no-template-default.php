<div id="rinkerid<?php echo esc_attr( $post_id )?>" class="yyi-rinker-contents yyi-rinker-postid-<?php echo esc_attr( $post_id )?> yyi-rinker-no-item">
	<div class="yyi-rinker-box">
		<div class="yyi-rinker-image"></div>
		<div class="yyi-rinker-info">
			<div class="yyi-rinker-title">
				<?php if ( strlen( $meta_datas[ 'title' ] ) > 0 ) { ?>
				<?php echo esc_html( $meta_datas[ 'title' ] ) ?>
				<?php } ?>
			</div>

			<div class="yyi-rinker-detail">
				<?php if ( isset( $credit) ) { ?>
					<div class="credit"><?php echo $credit ?></div>
				<?php } ?>
				<?php if ( strlen( $meta_datas[ 'brand' ] ) > 0 ) { ?>
					<div class="brand"><?php echo esc_html( $meta_datas[ 'brand' ] ); ?></div>
				<?php } ?>
			</div>
			<ul class="yyi-rinker-links">
				<?php if( isset( $meta_datas[ 'free_url_1' ] ) &&  strlen( $meta_datas[ 'free_url_1' ] ) > 0 ) { ?>
					<li class="freelink1">
						<?php echo ($meta_datas[ 'free_url_1' ]) ?>
					</li>
				<?php } ?>
				<?php if( isset( $meta_datas[ 'free_url_3' ] ) &&  strlen( $meta_datas[ 'free_url_3' ] ) > 0 ) { ?>
                    <li class="freelink3">
						<?php echo ($meta_datas[ 'free_url_3' ]) ?>
                    </li>
				<?php } ?>
				<?php if ( isset( $meta_datas[ 'amazon_url' ] ) &&  strlen( $meta_datas[ 'amazon_url' ] ) > 0 ) { ?>
					<li class="amazonlink">
						<?php echo  isset( $meta_datas[ 'amazon_link' ] ) ?  $meta_datas[ 'amazon_link' ] : '';?>
					</li>
				<?php } ?>
				<?php if ( isset( $meta_datas[ 'rakuten_url' ] ) &&  strlen( $meta_datas[ 'rakuten_url' ] ) > 0 ) { ?>
					<li class="rakutenlink">
						<?php echo  isset( $meta_datas[ 'rakuten_link' ] ) ?  $meta_datas[ 'rakuten_link' ] : '';?>
					</li>
				<?php } ?>
				<?php if ( isset( $meta_datas[ 'yahoo_url' ] ) && strlen( $meta_datas[ 'yahoo_url' ] ) > 0 ) { ?>
					<li class="yahoolink">
						<?php echo  isset( $meta_datas[ 'yahoo_link' ] ) ?  $meta_datas[ 'yahoo_link' ] : '';?>
					</li>
				<?php } ?>
				<?php if ( isset( $meta_datas[ 'free_url_2' ] ) &&  strlen( $meta_datas[  'free_url_2' ] ) > 0 ) { ?>
					<li class="freelink2">
						<?php echo $meta_datas[ 'free_url_2' ] ?>
					</li>
				<?php } ?>
				<?php if( isset( $meta_datas[ 'free_url_4' ] ) &&  strlen( $meta_datas[ 'free_url_4' ] ) > 0 ) { ?>
                    <li class="freelink4">
						<?php echo ($meta_datas[ 'free_url_4' ]) ?>
                    </li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>