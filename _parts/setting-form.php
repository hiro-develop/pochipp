<?php
do_action( $this->add_prefix( 'insert_html_first_form' ), $params );
?>
<h1>Rinker設定</h1>
<form method="POST" action="">
	<h2>設定</h2>
	<h3>基本設定</h3>
	<p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="<?php echo 'is_no_reapi'; ?>_form">商品情報の再取得</label></th>
			<td>
				<?php $is_noapi_checked = ! ! get_option( $this->option_column_name( 'is_no_reapi' ), false ); ?>
				<div class="radio-box">
					<input id="<?php echo 'is_no_reapi'; ?>_0_form" type="radio" value="0" name="<?php echo 'is_no_reapi'; ?>" 
										  <?php
					if ( ! $is_noapi_checked ) {
												?>
checked="checked" <?php } ?>><label for="<?php echo 'is_no_reapi'; ?>_0_form">再取得をする</label>
					<?php $is_checked = ! ! get_option( $this->option_column_name( 'is_no_price_disp_column' ), false ); ?>
					<input id="<?php echo 'is_no_price_disp_column'; ?>_form" type="checkbox" value="1" name="<?php echo 'is_no_price_disp_column'; ?>" 
										  <?php
					if ( $is_checked ) {
												?>
checked="checked" <?php } ?>>
					<label for="<?php echo 'is_no_price_disp_column'; ?>_form">価格を非表示にする</label>
				</div>
				<div class="radio-box">
					<input id="<?php echo 'is_no_reapi'; ?>_1_form" type="radio" value="1" name="<?php echo 'is_no_reapi'; ?>" 
										  <?php
					if ( $is_noapi_checked ) {
												?>
checked="checked" <?php } ?>><label for="<?php echo 'is_no_reapi'; ?>_1_form">再取得をしない</label>
				</div>
				<div><span class="yyi-relink-message">※再取得しないを選択した場合[価格は非表示][リンク切れチェックは無し]になります。PA-APIの利用が停止された場合チェックをつけてください。</span></div>
			</td>
		</tr>
		</tbody>
	</table>
	</p>
	<h3>Amazon</h3>
	<h4>API</h4>
	<p>
		Rinkerを利用するためにはAmazonのAmazon Product Advertising APIの認証キーを取得する必要があります。<br />
		<a href="https://affiliate.amazon.co.jp/assoc_credentials/home" target="_blank">Amazon Product Advertising APIの認証キーを取得</a>からキーを取得して、アクセスキーID、シークレットキーを登録してください。<br />
		認証キーの詳しい取得方法は<a href="https://oyakosodate.com/rinker/getamazonapikey/" target="_blank">Amazon Product Advertising APIの認証キー取得方法</a>にも記載しています。
	</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="amazon_access_key_form">アクセスキーID</label></th>
			<td>
				<input id="amazon_access_key_form" type="text" name="amazon_access_key" value="<?php echo esc_attr( $this->array_get( $params, 'amazon_access_key', '' ) ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<label for="amazon_secret_key_form">シークレットキー</label>
			</th>
			<td>
				<input id="amazon_secret_key_form" type="text" name="amazon_secret_key" value="<?php echo esc_attr( $this->array_get( $params, 'amazon_secret_key', '' ) ); ?>" />
			</td>
		</tr>
		</tbody>
	</table>
	<h4>アソシエイツのトラッキングID</h4>
	<p>利用できるトラッキングIDは<a href="https://affiliate.amazon.co.jp/home/account/tag/manage">トラッキングIDの管理</a>から確認できます。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="amazon_traccking_id_form">トラッキングID</label>
			</th>
			<td>
				<input id="amazon_traccking_id_form" type="text" name="amazon_traccking_id" value="<?php echo esc_attr( $this->array_get( $params, 'amazon_traccking_id', '' ) ); ?>" />
			</td>
		</tr>
		</tbody>
	</table>

	<h4>リンク先</h4>

	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="is_detail_amazon_url_0">Amazonボタン</label>
			</th>
			<td>
				<?php $is_amazon_detail_checked = $this->is_amazon_detail_url(); ?>
				<input id="is_detail_amazon_url_0" type="radio"  name="<?php echo 'is_detail_amazon_url'; ?>" 
																				  <?php
				if ( ! $is_amazon_detail_checked ) {
																																		?>
checked="checked" <?php } ?> value="0">
				<label  for="is_detail_amazon_url_0">リンク先を検索画面にする</label>
				<input id="is_detail_amazon_url_1" type="radio" name="<?php echo 'is_detail_amazon_url'; ?>" 
																				 <?php
				if ( $is_amazon_detail_checked ) {
																																		?>
checked="checked" <?php } ?> value="1">
				<label  for="is_detail_amazon_url_1">リンク先を商品の詳細画面にする</label>
			</td>
		</tr>
		</tbody>
	</table>
	<h4>共通設定</h4>
	<p>[Amazonから検索]で作成した商品リンクに表示されます。商品リンクの[フリーHTML]を設定している場合は商品リンクの[フリーHTML]が優先されます。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="amazon_free_comment">Amazon用フリーHTML</label>
			</th>
			<td>
				<div class="tag_insert_box">
					<input class="tag_insert_button" type="button" value="{{@amazon_id}}" id="insert_amazon_id">
					<span class="yyi-rinker-small">はAmazonのトラッキングIDに変換されます。</span>
				</div>
				<div class="tag_insert_box">
					<input class="tag_insert_button" type="button" value="{{@asin}}" id="insert_asin">
					<span class="yyi-rinker-small">はASINに変換されます。</span>
				</div>
				<textarea rows="3" id="amazon_free_comment" type="text" name="<?php echo 'amazon_free_comment'; ?>"><?php echo esc_textarea( stripslashes( $this->array_get( $params, 'amazon_free_comment', '' ) ) ); ?></textarea>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>楽天</h3>
	<p>楽天のアフィリエイトIDは<a href="https://webservice.rakuten.co.jp/account_affiliate_id/" target="_blank">楽天のアフィリエイトID</a>からアフィリエイトIDを調べて登録してください。<br />アプリIDの詳しい取得方法は<a href=" https://oyakosodate.com/rinker/getrakutenapplicationid/" target="_blank">楽天のアフィリエイトIDの取得方法</a>にも記載しています。</p>


	<h3>アフィリエイトID</h3>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="rakuten_affiliateid_form">アフィリエイトID</label>
			</th>
			<td>
				<input id="rakuten_affiliateid_form" type="text" name="rakuten_affiliate_id" value="<?php echo esc_attr( $this->array_get( $params, 'rakuten_affiliate_id', '' ) ); ?>" />
			</td>
		</tr>

		</tbody>
	</table>

	<h4>リンク先</h4>

	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="is_detail_rakuten_url_0">楽天市場ボタン</label>
			</th>
			<td>
				<?php $is_rakuten_detail_checked = $this->is_rakuten_detail_url(); ?>
				<input id="is_detail_rakuten_url_0" type="radio"  name="<?php echo 'is_detail_rakuten_url'; ?>" 
																				   <?php
				if ( ! $is_rakuten_detail_checked ) {
																																		?>
checked="checked" <?php } ?> value="0">
				<label  for="is_detail_rakuten_url_0">リンク先を検索画面にする</label>
				<input id="is_detail_rakuten_url_1" type="radio" name="<?php echo 'is_detail_rakuten_url'; ?>" 
																				  <?php
				if ( $is_rakuten_detail_checked ) {
																																		?>
checked="checked" <?php } ?> value="1">
				<label  for="is_detail_rakuten_url_1">リンク先を商品の詳細画面にする</label>
			</td>
		</tr>
		</tbody>
	</table>

	<h4>共通設定</h4>
	<p>[楽天市場から検索]で作成した商品リンクに表示されます。商品リンクの[フリーHTML]を設定している場合は商品リンクの[フリーHTML]が優先されます。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="rakuten_free_comment">楽天市場用フリーHTML</label>
			</th>
			<td>
				<div class="tag_insert_box">
					<input class="tag_insert_button" type="button" value="{{@rarakuten_id}}" id="insert_rakuten_id">
					<span class="yyi-rinker-small">は楽天市場のアフィリエイトIDに変換されます。</span>
				</div>
				<div class="tag_insert_box">
					<input class="tag_insert_button" type="button" value="{{@rarakuten_code}}" id="insert_rakuten_code">
					<span class="yyi-rinker-small">は楽天市場商品コードに変換されます。</span>
				</div>
				<textarea rows="3" id="rakuten_free_comment" type="text" name="<?php echo 'rakuten_free_comment'; ?>"><?php echo esc_textarea( stripslashes( $this->array_get( $params, 'rakuten_free_comment', '' ) ) ); ?></textarea>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>Yahooショッピング（バリューコマース）設定</h3>
	<p><strong>[LinkSwitch]</strong>か<strong>[アフィリエイトID]</strong>どちらかを設定してください。両方設定しても動作します。</p>
	<h4>LinkSwitch</h4>
	<p>LinkSwitchタグの取得方法は<a href="https://oyakosodate.com/rinker/yahoolinkswitch/" target="_blank">Yahooショッピング用 LinkSwitchタグ取得方法</a>をごらんください。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="valuecommerce_linkswitch_tag_form">LinkSwitch</label>
			</th>
			<td>
				<textarea rows="3" id="valuecommerce_linkswitch_tag_form" type="text" name="<?php echo 'valuecommerce_linkswitch_tag'; ?>"><?php echo esc_textarea( stripslashes( $this->array_get( $params, 'valuecommerce_linkswitch_tag', '' ) ) ); ?></textarea>
			</td>
		</tr>
		</tbody>
	</table>

	<h4>アフィリエイトID</h4>
	<p>pidとsidの取得方法は<a href="https://oyakosodate.com/yahoopidsid/" target="_blank">Yahooショッピング用 pidとsidの取得方法</a>をごらんください。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="yahoo_sid_form">sid</label>
			</th>
			<td>
				<input id="yahoo_sid_form" type="text" name="yahoo_sid" value="<?php echo esc_attr( $this->array_get( $params, 'yahoo_sid', '' ) ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<label for="yahoo_pid_form">pid</label>
			</th>
			<td>
				<input id="yahoo_pid_form" type="text" name="yahoo_pid" value="<?php echo esc_attr( $this->array_get( $params, 'yahoo_pid', '' ) ); ?>" />
			</td>
		</tr>
		</tbody>
	</table>

	<h3>もしもアフィリエイト設定</h3>
	<p>IDの取得方法は<a href="https://oyakosodate.com/rinker/getmoshimoid/" target="_blank">もしもIDの取得方法</a>にも記載しています。<br>[楽天市場]はもしも優先にすると<strong>すべてのリンクが検索画面へのリンク</strong>になります。</p>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="moshimo_amazon_id_form">AmazonID</label>
			</th>
			<td>
				<input id="moshimo_amazon_id_form" type="text" name="<?php echo 'moshimo_amazon_id'; ?>" value="<?php echo esc_attr( $this->array_get( $params, 'moshimo_amazon_id', '' ) ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<label for="moshimo_rakuten_id_form">楽天ID</label>
			</th>
			<td>
				<input id="moshimo_rakuten_id_form" type="text" name="<?php echo 'moshimo_rakuten_id'; ?>" value="<?php echo esc_attr( $this->array_get( $params, 'moshimo_rakuten_id', '' ) ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<label for="moshimo_yahoo_id_form">YahooショッピングID</label>
			</th>
			<td>
				<input id="moshimo_yahoo_id_form" type="text" name="<?php echo 'moshimo_yahoo_id'; ?>" value="<?php echo esc_attr( $this->array_get( $params, 'moshimo_yahoo_id', '' ) ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<label for="moshimo_shops_check">もしもリンク優先ショップ</label>
			</th>
			<td>
				<?php $digit = $this->array_get( $params, 'moshimo_shops_check', 0 ); ?>
				<?php foreach ( $this->shop_types as $shop_type => $values ) { ?>
					<?php $is_checked = $digit & $values['val'] ? ' checked="checked"' : ''; ?>
					<input id="moshimo_shops_check_<?php echo esc_attr( $values['val'] ); ?>" type="checkbox" name="<?php echo 'moshimo_shops_check'; ?>[]" value="<?php echo esc_attr( $values['val'] ); ?>" <?php echo $is_checked; ?> />
					<label for="moshimo_shops_check_<?php echo esc_attr( $values['val'] ); ?>"><?php echo esc_html( $values['label'] ); ?></label>
				<?php } ?>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>Google Analytics トラッキング</h3>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="valuecommerce_linkswitch_tag_form">Google Analytics</label>
			</th>
			<td>
				<?php $is_checked = ! ! get_option( $this->option_column_name( 'is_tracking' ), false ); ?>
				<input id="is_tracking" type="checkbox" value="1" name="<?php echo 'is_tracking'; ?>" 
																				   <?php
				if ( $is_checked ) {
																																		?>
checked="checked" <?php } ?>>
				<label  for="is_tracking">商品リンクのクリックをトラッキング</label>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>デザイン設定</h3>
	<table class="form-table">
		<tbody>
		<tr>
			<th>
				<label for="valuecommerce_linkswitch_tag_form">デザイン</label>
			</th>
			<td>
				<?php $design_type_val = intval( get_option( $this->option_column_name( 'design_type' ), self::DESIGN_TYPE_NORMAL ) ); ?>
				<select name="<?php echo 'design_type'; ?>">
					<?php foreach ( $this->design_types as $i => $val ) { ?>
						<?php $is_checked = $design_type_val === $i ? ' selected="selected"' : ''; ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php echo $is_checked; ?>>
						<?php echo esc_html( $val['label'] ); ?>
					</option>
					<?php } ?>
				</select>
			</td>
		</tr>
		</tbody>
	</table>

	<?php
	do_action( $this->add_prefix( 'insert_html_last_form' ), $params );

	wp_nonce_field( $this->_admin_referer_column );
	?>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存">
	</p>
</form>


<div class="setting-form-box">
	<h2>キャッシュ削除</h2>
	<p>商品情報は24時間キャッシュに保存しています。[商品情報キャッシュを全て削除する]ボタンを押すと全てのキャッシュが削除されます。</p>
	<p>個別に削除したい場合は削除したい[商品リンク]のデータを[更新]してください。</p>
	<p class="submit">
		<input id="delete-cache" type="button" name="delete-cache" class="button button-primary" value="商品情報キャッシュを全て削除する"><span class="message" id="delete-cachemessage"></span>
	</p>
</div>


<p><a href="https://affiliate.amazon.co.jp/help/operating/paapilicenseagreement" target="_blank">Amazon.co.jp Product Advertising API ライセンス契約</a></p>

<p><a href="https://affiliate.amazon.co.jp/help/topic/t32/ref=amb_link_4DzstEfuM3il9tu_VfGMaw_3?pf_rd_p=cbe5b1ea-57a4-41b3-952a-34cb16b7abfb" target="_blank">Product Advertising API (PA-API) の利用ガイドライン</a></p>

<div id="yyi-loading">
	<img src="<?php echo esc_attr( $this->loading_img_url ); ?>">
</div>

<script type="text/javascript">

	(function ($) {
		$(document).on('click', '#delete-cache', function (event) {
			$('div#yyi-loading img').show();
			var params = {
				action: 'yyi_rinker_delete_all_cache',
			};
			params['_wpnonce'] = '<?php echo wp_create_nonce( $this->add_prefix( 'delete_all_cache' ) ); ?>';
			$.ajax({
				url: '<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php',
				method: 'POST',
				data: params,
			}).done(function (data, textStatus, jqXHR) {
				if (isFinite(data)) {
					$('#delete-cachemessage').text('キャッシュを削除しました');
				} else {
					console.log(data);
					$('#delete-cachemessage').text('キャッシュの削除に失敗しました');
				}

			}).always(function(jqXHR, textStatus){
				$('div#yyi-loading img').hide();
			});

		});

		$('#insert_amazon_id').click(function() {
			yyi_rinker_insert_tag($('#amazon_free_comment'), '<?php echo esc_js( self::AMAZON_ID_INSERT_TAG ); ?>');
		});
		$('#insert_asin').click(function() {
			yyi_rinker_insert_tag($('#amazon_free_comment'), '<?php echo esc_js( self::ASIN_INSERT_TAG ); ?>');
		});

		$('#insert_rakuten_id').click(function() {
			yyi_rinker_insert_tag($('#rakuten_free_comment'), '<?php echo esc_js( self::RAKUTEN_ID_INSERT_TAG ); ?>');
		});

		$('#insert_rakuten_code').click(function() {
			yyi_rinker_insert_tag($('#rakuten_free_comment'), '<?php echo esc_js( self::RAKUTEN_CODE_INSERT_TAG ); ?>');
		});

	})(jQuery);

</script>
