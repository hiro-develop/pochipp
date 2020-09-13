<div id="<?php echo( $this->prefix ); ?>">
	<div id="yyi-link-add-media-button" class="wp-media-buttons">
		<a id="yyirinker-media-button" href="<?php echo esc_attr( $src ); ?>" class="button thickbox add_media" title="商品情報を取得"><span class="yyirinker-buttons-icon"></span>商品情報を取得</a>
	</div>
	<div id="yyi-link-custom-fields">
		<?php foreach($this->custom_field_params AS $index => $values) { ?>
			<div class="yyi-link-custom-field-item <?php echo $values[ 'is_link' ] ?  'relink' : ''; ?> <?php echo $values[ 'is_free' ] ?  'free' : 'not_free'; ?>">
				<label class="linklabel" for="<?php echo esc_attr( $values[ 'key' ] ); ?>">
					<?php echo esc_html( $values[ 'label' ] ); ?>
				</label>
				<?php if ( 'search_shop_value' === $values[ 'key' ]) {
					$value = intval( get_post_meta( $post->ID, $this->custom_field_column_name( $values[ 'key' ] ), true ) );
					foreach( $this->search_shops AS $index => $shop_name ) {
						if ( $value === $index || ( $value === 0 && $index == 10 ) ) {
							$checked = 'checked="checked"';
						} else {
							$checked = '';
						}
						?>
						<input id="<?php echo esc_attr( $values[ 'key' ] . $index); ?>" type="radio" name="<?php echo esc_attr( $values[ 'key' ] ); ?>" value="<?php echo esc_attr( $index ) ?>" <?php echo $checked ?> />
						<label for="<?php echo esc_attr( $values[ 'key' ] . $index); ?>"><?php echo esc_html( $shop_name )?></label>
					<?php } ?>
				<?php } elseif ( 'is_amazon_no_exist' === $values[ 'key' ] || 'is_rakuten_no_exist' === $values[ 'key' ] ) { ?>
					<?php $checked =  !!get_post_meta($post->ID, $this->custom_field_column_name( $values[ 'key' ] ), true) ? 'checked="checked"' : ''; ?>
					<input id="<?php echo esc_attr( $values[ 'key' ] ); ?>" type="checkbox" name="<?php echo esc_attr( $values[ 'key' ] ); ?>" value="1" <?php echo $checked ?> />
				<?php } elseif ($values[ 'is_text' ] ) { ?>
					<div>
						<textarea id="<?php echo esc_attr( $values[ 'key' ] ); ?>" name="<?php echo esc_attr( $values[ 'key' ] ); ?>"><?php echo esc_textarea(get_post_meta($post->ID, $this->custom_field_column_name( $values[ 'key' ] ), true )) ?></textarea>
						<span class="yyi-rinker-small">記入したHTMLは商品リンクボタンの上に表示されます。</span>
					</div>
					<div class="tag_insert_box">
						<div>
							<input class="tag_insert_button" type="button" value="{{@amazon_id}}" id="insert_amazon_id">
							<span class="yyi-rinker-small">はAmazonのトラッキングIDに変換されます。</span>
						</div>
						<div>
							<input class="tag_insert_button" type="button" value="{{@asin}}" id="insert_asin">
							<span class="yyi-rinker-small">はASINに変換されます。</span>
						</div>
						<div>
							<input class="tag_insert_button" type="button" value="{{@rarakuten_id}}" id="insert_rakuten_id">
							<span class="yyi-rinker-small">は楽天市場のアフィリエイトIDに変換されます。</span>
						</div>
						<div>
							<input class="tag_insert_button" type="button" value="{{@rarakuten_code}}" id="insert_rakuten_code">
							<span class="yyi-rinker-small">は楽天市場商品コードに変換されます。</span>
						</div>
				</div>
                <?php } else { ?>
				<input class="<?php if( $values[ 'is_size' ] ){ ?>yyi-rinker-img-size<?php } ?>" id="<?php echo esc_attr( $values[ 'key' ] ); ?>" type="text" name="<?php echo esc_attr( $values[ 'key' ] ); ?>" value="<?php echo esc_attr( get_post_meta($post->ID, $this->custom_field_column_name( $values[ 'key' ] ), true) ) ?>" />
				<?php if( $values[ 'is_size' ] ): ?>px<?php endif; ?>
				<?php if( $values[ 'is_img' ] ): ?><button class="image_url" id="<?php echo esc_attr( $values[ 'key' ] ); ?>_button">画像を選択</button><?php endif; ?>
				<?php } ?>
				<?php if ( $values[ 'is_link' ] ) { ?>
				<a class="yyi-rinker-confirm-link">確認</a>
				<?php } elseif ( $values[ 'key' ] === 'keyword' ) { ?>
					で以下のURLを<a class="yyi-rinker-relink">更新</a><span class="yyi-relink-message"></span>
				<?php }?>

			</div>
		<?php }?>

		<input id="yyi_rinker_from_page" type="hidden" name="yyi_rinker_from_page" value="main"/>

		<input id="yyi_post_contents" type="hidden" name="content" value=""/></div>

		<div id="yyi-loading">
			<img src="<?php echo esc_attr( $this->loading_img_url ); ?>">
		</div>
	</div>


<script type="text/javascript">
	(function ($) {
		$(document).ready(function(){
			yyi_add_shortcode_content();
            toggle_shop_value_form();
		});
		$('#submitdiv').click(function() {
			yyi_add_shortcode_content();
		});

		$('input[name="search_shop_value"]').click(function ( event ) {
            toggle_shop_value_form();
		});

        $('input[name=post_title]').change(function() {
			yyi_add_shortcode_content();
		});

		$('div#yyi_afilinks_middle_link input').change(function() {
			yyi_add_shortcode_content();
		});

		yyi_add_shortcode_content = function() {
			var title = $('input[name=post_title]').val();
			title = title.replace('[', '【');
			title = title.replace(']', '】');
			var shortcode = '[itemlink title="'+ title +  '"]';
			$('#yyi_post_contents').val(shortcode);
		}

		$('#insert_amazon_id').click(function() {
            yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::AMAZON_ID_INSERT_TAG ) ?>');
        });

        $('#insert_asin').click(function() {
            yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::ASIN_INSERT_TAG ) ?>');
        });

        $('#insert_rakuten_id').click(function() {
            yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::RAKUTEN_ID_INSERT_TAG ) ?>');
        });

        $('#insert_rakuten_code').click(function() {
            yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::RAKUTEN_CODE_INSERT_TAG ) ?>');
        });

		//URLを貼り直す
		$('a.yyi-rinker-relink').click(function() {
			var element = $(this);
			var keywords = element.parent().find('input[name="<?php echo 'keyword' ?>"]').val();
			var message = $('.yyi-relink-message');

			var params = {
				action: 'yyi_rinker_relink',
				keywords: keywords,
			};

			$('div#yyi-loading img').show();
			$('.yyi-message').text('');

			$.ajax({
				url: '<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php',
				method: 'GET',
				dataType: 'json',
				data: params,
			}).done(function (data, textStatus, jqXHR) {
				$('#amazon_url').val(data.amazon_url);
				$('#rakuten_url').val(data.rakuten_url);
				$("#yahoo_url").val(data.yahoo_url);
				message.text('リンクを更新をしました');
			}).fail(function(jqXHR, textStatus, errorThrown){
				message.text('通信エラーで更新できませんでした');
			}).always(function (jqXHR, textStatus) {
				$('div#yyi-loading img').hide();
			});
		});

		$('.image_url').click(function(e) {
            let uploader;
			e.preventDefault();
            uploader = wp.media({
				title: '画像選択',
				library: {
					type: 'image'
				},
				button: {
					text: '画像選択'
				},
				multiple: false
			});
			let button_id = $(this).attr('id');
			let url_form_id = button_id.replace('_button', '');
            let width_form_id = '';
            let height_form_id = '';
			switch ( url_form_id ) {
				case 's_image_url':
					width_form_id = 'image_s_size_w_column';
					height_form_id = 'image_s_size_h_column';
					break;
				case 'm_image_url':
					width_form_id = 'image_m_size_w_column';
					height_form_id = 'image_m_size_h_column';
					break;
				case 'l_image_url':
					width_form_id = 'image_l_size_w_column';
					height_form_id = 'image_l_size_h_column';
					break;
			}
			uploader.on('select', function() {
				var images = uploader.state().get('selection');
				images.each(function(file){
					$('#' + url_form_id).val(file.toJSON().url);
					$('#' + width_form_id).val(file.toJSON().width);
					$('#' + height_form_id).val(file.toJSON().height);
				});
			});
            uploader.open();
		});

		function toggle_shop_value_form() {
			if($('#search_shop_value<?php echo esc_js(self::SEARCH_SHOP_FREE )?>').prop('checked') === true) {
				$('.not_free input').each(function(index, element){
					$(element).val('');
					$(element).prop('checked', false);
				});
				$('#yyirinker-media-button').hide();
				$('.not_free').hide();
				$('.free').show();
			} else {
				$('#yyirinker-media-button').show();
				$('.not_free').show();
			}
		}

	})(jQuery);
</script>

