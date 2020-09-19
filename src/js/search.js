/**
 * thickboxで呼び出される iframe の中で読み込むスクリプト
 */

/**
 * 検索結果のデータをHTMLに変換
 *
 * @param {Array} itemDatas 検索結果データ
 * @param {string} type 'registerd' or 'searched'
 */
const getResultHtml = (itemDatas, type) => {
	let result = '';

	Object.keys(itemDatas).forEach((index) => {
		const item = itemDatas[index];
		const price = Number(item.price);

		// 商品詳細ページがあればそっち、なければキーワード検索画面
		const amazonLink = item.amazon_url || item.amazon_url;
		const rakutenLink = item.rakuten_title_url || item.rakuten_url;

		result += `<div class="pochipp-item" data-index="${index}" data-type="${type}">`;
		result += `
			<div class="pochipp-item__img">
				<img src="${item.s_image_url}" alt="" />
			</div>
			<div class="pochipp-item__body">
				<div class="pochipp-item__title">${item.title}</div>
				<div class="pochipp-item__brand">ブランド：${item.brand}</div>
				<div class="pochipp-item__price">価格：¥${price.toLocaleString()}</div>
				<div class="pochipp-item__links">
					商品ページ：
					<a href="${amazonLink}" rel="nofollow noreferrer" target="_blank">Amazonで確認</a>
					<a href="${rakutenLink}" rel="nofollow noreferrer" target="_blank">楽天で確認</a>
				</div>
		`;

		// ボタン
		if ('registerd' === type) {
			const adminUrl = window.pochippIframeVars.adminUrl;
			const editUrl = `${adminUrl}post.php?post=${item.post_id}&action=edit`;

			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
				<a class="button" data-pochipp="edit" href="${editUrl}" rel="nofollow noreferrer" target="_blank">この商品を編集</a>
			</div>`;
		} else {
			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
			</div>`;
		}

		result += `</div></div>`;
	});

	return `<div class="pochipp-items">${result}</div>`;
};

(function ($) {
	// console.log(window.pochippIframeVars);

	// 情報を取得
	const { ajaxUrl, tabKey, blockId, calledAt } = window.pochippIframeVars;

	// キーワード入力欄へフォーカスさせる
	const $keywords = $('#keywords');
	$keywords.focus();

	// フォームの送信イベント
	$('#search_form').submit(function (e) {
		e.preventDefault();
		// console.log('search start!');

		if ($keywords.val() === '') {
			$('#result_area').html('<p>キーワードを入力して下さい。</p>');
			return;
		}

		// 検索エリアの描画をリセット
		$('#result_area').html('');

		// ajaxに投げるデータ
		const params = {};
		params.action = tabKey || 'pochipp_search_amazon'; // タブキーがそのままアクション名
		params.keywords = $('#keywords').val();
		params.search_index = $('#search_index').val(); // Amazonのカテゴリー
		params.sort = $('#sort_select').val(); // 並び順
		params.page = 1;

		// ajax実行
		$.ajax({
			url: ajaxUrl,
			dataType: 'json',
			data: params,
			beforeSend: () => {
				// ローディング画像の表示開始
				$('#loading_image').show();
			},
		})
			.done(function (datas, textStatus, jqXHR) {
				// $('#yyi-rinker-search-result').empty();

				// 検索結果
				const searchedItems = datas.searched_items;

				// 取得済みデータ
				const registerdItems = datas.registerd_items;

				let resultHtml = '';
				if ('editor' === calledAt) {
					// 投稿編集画面での呼び出し時のみ、登録済み商品を表示。
					resultHtml +=
						'<div class="pcpp-tb__area-title">登録済み</div>';
					resultHtml += getResultHtml(registerdItems, 'registerd');
				}

				// 普通の検索結果データを表示
				resultHtml += '<div class="pcpp-tb__area-title">検索結果</div>';
				resultHtml += getResultHtml(searchedItems, 'searched');

				// console.log('searchedItems', searchedItems);
				// console.log('registerdItems', registerdItems);

				$('#result_area').html(resultHtml);

				// 「商品選択ボタン」のクリックイベントを登録
				$('[data-pochipp="select"]').click(function (e) {
					const $thisItem = $(this).parents('.pochipp-item');
					const itemIndex = $thisItem.attr('data-index');
					const itemtype = $thisItem.attr('data-type');
					const itemData =
						'registerd' === itemtype
							? registerdItems[itemIndex]
							: searchedItems[itemIndex];

					// タイトル情報だけ別枠で渡す
					const itemTitle = itemData.title || 'No Title';
					delete itemData.title;

					window.top.set_block_data(itemTitle, itemData, blockId);
					window.parent.tb_remove();
				});
			})
			.always(function (jqXHR, textStatus) {
				// ローディング画像の表示終了
				$('#loading_image').hide();
			});

		return false;
	});
})(window.jQuery);
