<?php
global $post_ID;

// http://shop.wp/wp-admin/media-upload.php?&post_id=18&type=yyi_rinker&tab=yyi_rinker_search_amazon&from=yyi_rinker&TB_iframe=true&width=753&height=804

$media_btn_src  = 'media-upload.php?post_id=' . $post_ID;
$media_btn_src .= '&type=pochipp'; // フックのアクション名と同じにしないとだめなやつ？
$media_btn_src .= '&tab=' . \POCHIPP::TABKEY_AMAZON;
$media_btn_src .= '&from=' . \POCHIPP::POST_TYPE_SLUG;
$media_btn_src .= '&TB_iframe=true';
?>


<div id="pochipp_metabox">
	<div id="yyi-link-add-media-button_" class="wp-media-buttons">
		<a id="yyirinker-media-button_" href="<?=esc_attr( $media_btn_src )?>" class="button thickbox add_media" title="商品情報を取得">
			<span class="yyirinker-buttons-icon"></span>商品情報を取得
		</a>
	</div>

	<div id="yyi-link-custom-fields">
		<input id="yyi_rinker_from_page" type="hidden" name="yyi_rinker_from_page" value="main"/>
		<input id="yyi_post_contents" type="hidden" name="content" value=""/></div>
		<!-- <div id="yyi-loading">
			<img src="<?php echo esc_attr( '' ); ?>">
		</div> -->
	</div>
</div>
<?php return; ?>

<script type="text/javascript">
	(function ($) {

		// yyi: yyi_rinker_insert_tag は {{@hoge}}をクリックで挿入するってだけ
		// admin-rinker.jsに関数の処理は書かれている。（全部そっちでいい気がする）
		$('#insert_amazon_id').click(function() {
			yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::AMAZON_ID_INSERT_TAG ); ?>');
		});

		$('#insert_asin').click(function() {
			yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::ASIN_INSERT_TAG ); ?>');
		});

		$('#insert_rakuten_id').click(function() {
			yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::RAKUTEN_ID_INSERT_TAG ); ?>');
		});

		$('#insert_rakuten_code').click(function() {
			yyi_rinker_insert_tag($('#free_comment'), '<?php echo esc_js( self::RAKUTEN_CODE_INSERT_TAG ); ?>');
		});

		// URLを貼り直す「更新」ボタン
		$('a.yyi-rinker-relink').click(function() {
			var element = $(this);
			var keywords = element.parent().find('input[name="<?php echo 'keyword'; ?>"]').val();
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



	})(jQuery);
</script>
