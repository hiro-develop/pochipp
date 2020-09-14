/**
 * thickboxで呼び出される iframe の中で読み込むスクリプト
 */
// console.log('search.js!');
// console.log(window.pochippIframeVars);

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
			const adminUrl = window.pochippIframeVars.admin_url;
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
	// キーワード入力欄へフォーカスさせる
	const $keywords = $('#keywords');
	$keywords.focus();

	const form = $('#search_form');

	// フォームの送信イベント
	form.submit(function (e) {
		e.preventDefault();

		if ($keywords.val() === '') {
			$('#result_area').html('<p>キーワードを入力して下さい。</p>');
			return;
		}

		// 検索エリアの描画をリセット
		$('#result_area').html('');

		console.log('search start!');

		// 情報を取得
		const blockid = $('[nama="blockid"]').val();
		const date = $('[nama="date"]').val();

		// タブを取得
		const nowTabKey = $('[nama="tab"]').val();

		// タブの状況から ajax アクション名を取得
		const actionName = nowTabKey || 'pochipp_search_amazon';

		const params = {};
		params.action = actionName;
		params.keywords = $('#keywords').val();
		params.search_index = $('#search_index').val();
		params.page = 1;
		params.sort = $('#sort_select').val();

		// ローディング画像の表示開始
		$('#loading_image').show();

		$.ajax({
			url: form.attr('action'),
			dataType: 'json',
			data: params,
		})
			.done(function (datas, textStatus, jqXHR) {
				// $('#yyi-rinker-search-result').empty();

				// 検索結果
				const searchedItems = datas.searched_items;

				// 取得済みデータ
				const registerdItems = datas.registerd_items;

				console.log('searchedItems', searchedItems);
				console.log('registerdItems', registerdItems);

				let resultHtml =
					'<div class="pcpp-tb__area-title">登録済み</div>';
				resultHtml += getResultHtml(registerdItems, 'registerd');
				resultHtml += '<div class="pcpp-tb__area-title">検索結果</div>';
				resultHtml += getResultHtml(searchedItems, 'searched');

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

					window.top.set_block_data(itemTitle, itemData, blockid);
					window.parent.tb_remove();
				});
			})
			.always(function (jqXHR, textStatus) {
				// ローディング画像の表示終了
				$('#loading_image').hide();
			});

		return false;
	});

	// タブを取得
	// const blockid = $('[nama="blockid"]').val();
	// const date = $('[nama="date"]').val();
	// window.top.set_block_data(date, blockid);
	// window.parent.tb_remove();

	//
})(window.jQuery);
