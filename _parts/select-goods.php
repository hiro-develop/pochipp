<?php
// 商品検索部分の中身
media_upload_header();

$tab = $this->array_get( $_GET, 'tab', self::TAB_AMAZON );
$cid = $this->array_get( $_GET, 'cid', '' );
?>
<div id="<?php echo( $this->prefix ); ?>">
	<div class="search-box" >
		<form id="search_form" method="GET" action="<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php">
			<input type="hidden" nama="tab" value="<?php echo esc_attr( $tab ); ?>"/>
			<input type="hidden" nama="action" value="search_amazon">
			<?php do_action( $this->add_prefix( 'add_terms_select_for_search' ), $tab ); ?>
			<?php if ( $tab === self::TAB_AMAZON ) { ?>
			<select id="search_index" name="search_index">
			<?php foreach ( $this->search_indexes as $key => $val ) { ?>
				<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $val ); ?></option>
			<?php } ?>
			</select>
			<?php } ?>
			<input id="keywords" type="text" name="keywords">
			<?php do_action( $this->add_prefix( 'add_sort_select_for_search' ), $tab ); ?>
			<input class="button" type="submit" value="検索">
		</form>
	</div>
	<ul tabindex="-1" class="attachments" id="yyi-rinker-search-result"><?php do_action( $this->add_prefix( 'search_result_items' ), $tab ); ?></ul>
	<div id="yyi-loading">
		<img src="<?php echo esc_attr( $this->loading_img_url ); ?>">
	</div>
</div>

<script type="text/javascript">


	(function ($){
		$('#keywords').focus();

		var form = $('#search_form');
		form.submit(function () {
			<?php if ( $tab === self::TAB_AMAZON ) { ?>
			yyi_rinker_amazon_search();
			<?php } elseif ( $tab === self::TAB_RAKUTEN ) { ?>
			yyi_rinker_rakuten_search();
			<?php } else { ?>
			yyi_rinker_search_itemlist();
			<?php } ?>
			return false;
		});

		<?php if ( $tab === self::TAB_ITEMLIST ) { ?>
		yyi_rinker_search_itemlist();
		<?php } ?>


		function yyi_rinker_amazon_search() {
			yyi_rinker_search_from_api('amazon');
		}

		function yyi_rinker_rakuten_search() {
			yyi_rinker_search_from_api('rakuten');
		}

		function yyi_rinker_search_from_api( shop ) {
			var params = {};
			params['action'] = 'yyi_rinker_search_' + shop;
			params['keywords'] = $('#keywords').val();
			params['search_index'] = $('#search_index').val();
			params['page'] = 1;
			params['sort'] = $('#sort_select').val();
			$('div#loading img').show();
			$.ajax({
					url: form.attr('action'),
					dataType: 'json',
					data: params,
				}).done(function (datas, textStatus, jqXHR) {
					$('#yyi-rinker-search-result').empty();
					var data = datas.api_datas;
					var old_data =datas.old_datas;
				<?php if ( ! isset( $_GET['from'] ) || $_GET['from'] !== self::LINK_POST_TYPE ) { ?>
					if (typeof old_data !== 'undefined') {
						$.each(old_data, function (i, value) {
							var li = '<li class="items old_datas">';
							li += '<div class="img"><img src="' + yyiRinkerEscapeHTML( value['<?php echo 's_image_url'; ?>'] ) + '"></div>';
							li += '<div class="detail"><div>（登録済み商品）</div><div class="title">' + yyiRinkerEscapeHTML( value['<?php echo 'title'; ?>'] ) + '</div>';
							li += '<div class="button-box"><a class="button" href="<?php echo esc_url( admin_url() ); ?>post.php?post=' + yyiRinkerEscapeHTML( value['post_id'] ) + '&action=edit" rel="nofollow" target="_blank">リンク編集</a><button class="button select add-items-from-list" data-item-post-id="' + yyiRinkerEscapeHTML( value['post_id'] ) + '" >商品リンクを追加</button>';
							<?php do_action( $this->add_prefix( 'add_select_goods_button_from_search' ), $tab ); ?>
							li += '</div>';
							li += '</div>';
							li += '</li>';
							$('#yyi-rinker-search-result').append(li);
						});
					}
					<?php } ?>
					if (typeof data !== 'undefined') {
						if (typeof data.error !== 'undefined') {
							var li = '<li class="error-item">' +
								'<span class="error-code">【エラー】</span>' + yyiRinkerEscapeHTML(data.error.message_jp) + ' ' +
								yyiRinkerEscapeHTML(data.error.code) +
								'</li>';
							$('#yyi-rinker-search-result').append(li);
							return;
						}
						if (data.length === 0) {
							var li = '<li class="no-item">商品はありません</li>';
							$('#yyi-rinker-search-result').append(li);
							return;
						}

						$.each(data, function (i, value) {
							var li = '<li class="items">';
							li += '<div class="img"><img src="' + yyiRinkerEscapeHTML( value['<?php echo 's_image_url'; ?>'] ) + '"></div>';
							li += '<div class="detail"><div class="title">'	+ yyiRinkerEscapeHTML( value['<?php echo 'title'; ?>'] );
							li += '</div>';
							if (typeof value['product_group'] !== 'undefined' && value['product_group'] === 'Digital Ebook Purchas') {
								li += '<div class="group">(Kindle)</div>';
							}
							if ( value['<?php echo 'price'; ?>'] !== '' ) {
								if (shop === 'rakuten') {
									li += '<div class="price">¥' + yyiRinkerEscapeHTML(yyiRinkerComma(value['<?php echo 'price'; ?>'])) + '（アフィリエイト利率:' + yyiRinkerEscapeHTML(value['affiliateRate']) + '%）（レビュー平均:' + yyiRinkerEscapeHTML(value['reviewAverage']) + '）</div>';
								} else {
									li += '<div class="price">¥' + yyiRinkerEscapeHTML(yyiRinkerComma(value['<?php echo 'price'; ?>'])) + '</div>';
								}
							}
							li += '<div class="button-box"><button class="button select add-items"';
							li += ' data-item-<?php echo 'title'; ?>="' + yyiRinkerEscapeHTML( value['<?php echo 'title'; ?>'] ) + '" ';
							<?php foreach ( $this->custom_field_params as $index => $value ) { ?>
							li += ' data-item-<?php echo esc_attr( $value['key'] ); ?>="' + yyiRinkerEscapeHTML( value['<?php echo $value['key']; ?>'] ) + '" ';
							<?php } ?>
							li += '>商品リンクを追加</button>';
							<?php do_action( $this->add_prefix( 'add_select_goods_button_from_api' ), $tab ); ?>
							li += '</div></div>';
							li += '</li>';
							$('#yyi-rinker-search-result').append(li);
						});
					}
				}).always(function(jqXHR, textStatus){
					$('div#loading img').hide();
				});

			return false;
		}

		//クリックするタイミングでDB登録
		$(document).on( 'click', '.add-items', function ( event ) {
			var element = $(this);
			var params = {
				action: 'yyi_rinker_add_item',
				title: element.data('item-title'),
			};
<?php
foreach ( $this->custom_field_params as $index => $value ) {
if ( $value['is_ajax'] ) {
		?>
			var val =  element.data('item-<?php echo $value['key']; ?>');
			if (val !== 'undefined') {
				params['<?php echo $value['key']; ?>'] = element.data('item-<?php echo $value['key']; ?>');
			}
		<?php
	}
}
?>
<?php if ( $tab === self::TAB_RAKUTEN ) { ?>
			params['<?php echo 'search_shop_value'; ?>'] = '<?php echo self::SEARCH_SHOP_RAKUTEN; ?>';
<?php } else { ?>
			params['<?php echo 'search_shop_value'; ?>'] = '<?php echo self::SEARCH_SHOP_AMAZON; ?>';
<?php } ?>
			params['keyword'] =  $('input#keywords').val();
			params['price_at'] =  new Date().toLocaleString();
			params['_wpnonce'] = '<?php echo wp_create_nonce( $this->add_prefix( 'add_itemlinks' ) ); ?>';

			//gutemberg用にブロック受取り
			var cid = '<?php echo esc_attr( $cid ); ?>';

<?php if ( isset( $_GET['from'] ) && $_GET['from'] === self::LINK_POST_TYPE ) { ?>
			yyi_rinker_set_value_from_parent(params);
			if (cid === '') {
				window.parent.yyi_add_shortcode_content();
			} else {
				$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').val(shortcode);
				$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').focus();
			}

			window.parent.tb_remove();
<?php } else { ?>
			//商品データを追加
			$.ajax({
				url: '<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php',
				method: 'POST',
				data: params,
			}).done(function (data, textStatus, jqXHR) {
					var text = '[itemlink post_id="' + data + '"]';
				//gutemberg用にブロック受取り
				var cid = '<?php echo esc_attr( $cid ); ?>';
				if (cid === '') {
					top.send_to_editor( text );
				} else {
					$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').val(text);
					$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').focus();
				}
			}).always(function (jqXHR, textStatus) {
				window.parent.tb_remove();
			});
<?php } ?>
		});

		//カスタムフィールドにデータをコピー
		function yyi_rinker_set_value_from_parent(params) {
			$.each(params, function( index, val ) {
				var value = val;
				switch ( index ) {
					case 'title':
						var input_metabox = $('input[name="post_title"]', parent.document);
						input_metabox.focus();
						input_metabox.val(value);
						break;
						case '<?php echo 'search_shop_value'; ?>':
						if (value == <?php echo self::SEARCH_SHOP_RAKUTEN; ?>) {
							$('#yyi_afilinks_middle_link input[id=search_shop_value<?php echo self::SEARCH_SHOP_RAKUTEN; ?>]', parent.document).prop("checked",true);
						} else {
							$('#yyi_afilinks_middle_link input[id=search_shop_value<?php echo self::SEARCH_SHOP_AMAZON; ?>]', parent.document).prop("checked",true);
						}
						break;
					default :
						var input_metabox = $('#yyi_afilinks_middle_link input[name="' + index + '"]', parent.document);
						input_metabox.val(value);
						break;
				}

			});
			$('input[id=is_amazon_no_exist]', parent.document).prop('checked', false);
			$('input[id=is_rakuten_no_exist]', parent.document).prop('checked', false);
		}

		//すでに登録してある商品から検索
		function yyi_rinker_search_itemlist() {
			var params = {};
			params['action'] = 'yyi_rinker_search_itemlist';
			params['keywords'] = $('#keywords').val();
			params['term_id'] = $('#term_select').val();
			$('div#yyi-loading img').show();
			$.ajax({
				url: '<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php',
				dataType: 'json',
				data: params,
			}).done(function (data, textStatus, jqXHR) {
				$('#yyi-rinker-search-result').empty();
				if (data.length === 0) {
					var li = '<li class="no-item">商品はありません</li>';
					$('#yyi-rinker-search-result').append(li);
					return;
				}
				$.each(data, function (i, value) {
					var li = '<li class="items">';
						li += '<div class="img"><img src="' + yyiRinkerEscapeHTML( value['<?php echo 's_image_url'; ?>'] ) + '"></div>';
						li += '<div class="detail"><div class="title">' + yyiRinkerEscapeHTML( value['<?php echo 'title'; ?>'] ) + '</div>';
						li += '<div class="links"><a class="button" href="' + yyiRinkerEscapeHTML( value['<?php echo 'amazon_url'; ?>'] ) + '" rel="nofollow" target="_blank">Amazon確認</a>';
						li += '<a class="button" href="' + yyiRinkerEscapeHTML( value['<?php echo 'rakuten_url'; ?>'] ) + '" rel="nofollow" target="_blank">楽天確認</a>';
						li += '<a class="button" href="' + yyiRinkerEscapeHTML( value['<?php echo 'yahoo_url'; ?>'] ) + '" rel="nofollow" target="_blank">Yahoo確認</a>';
						li += '<a class="button" href="<?php echo esc_url( admin_url() ); ?>post.php?post=' + yyiRinkerEscapeHTML( value['post_id'] ) + '&action=edit" rel="nofollow" target="_blank">リンク編集</a></div>';
						li += '<div class="button-box"><button class="button select add-items-from-list" data-item-post-id="' + yyiRinkerEscapeHTML( value['post_id'] ) + '" >商品リンクを追加</button>';
						<?php do_action( $this->add_prefix( 'add_select_goods_button_from_search' ), $tab ); ?>
						li += '</div>';
						li += '</div>';
						li += '</li>';
					$('#yyi-rinker-search-result').append(li);
				});
			}).always(function(jqXHR, textStatus){
				$('div#yyi-loading img').hide();
				<?php do_action( $this->add_prefix( 'add_always_api_from_search' ), $tab ); ?>
			});
		}

		//登録済みの商品リストから追加
		$(document).on( 'click', '.add-items-from-list', function ( event ) {
			var shortcode = '[itemlink post_id="' + $(this).data('item-post-id') + '"]';
			//gutemberg用にブロック受取り
			var cid = '<?php echo esc_attr( $cid ); ?>';
			if (cid === '') {
				top.send_to_editor( shortcode );
			} else {
				$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').val(shortcode);
				$('div#editor', window.parent.document).find("div#block-" + cid ).find('.rinkerg-richtext').focus();
			}
			window.parent.tb_remove();
		});

		<?php do_action( $this->add_prefix( 'add_select_goods_javascript_load' ), $tab ); ?>
	})(jQuery);

	function yyiRinkerEscapeHTML( str ) {
		if (typeof str !== 'undefined') {
			str = str + '';
			return str.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		} else {
			return '';
		}
	}
	
	function yyiRinkerComma( num ) {
		if (typeof num === 'undefined') {
			return '';
		} else {
			num = num + '';
			num = num.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
			return num;
		}
	}
</script>
