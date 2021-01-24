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

	// console.log('itemDatas', itemDatas);

	// エラーが返ってきている場合
	if (itemDatas.error) {
		return `<div class="pochipp-items--errot">${itemDatas.error.code} : ${itemDatas.error.message}</div>`;
	}

	Object.keys(itemDatas).forEach((index) => {
		const item = itemDatas[index];
		// console.log(item);

		const price = Number(item.price);

		// 商品詳細ページがあればそっち、なければキーワード検索画面
		const amazonLink = item.amazon_detail_url || '';
		const rakutenLink = item.rakuten_detail_url || '';

		let amazonBtn = '';
		if (amazonLink) {
			amazonBtn = `<a href="${amazonLink}" class="button" rel="nofollow noreferrer" target="_blank">Amazon商品ページを確認</a>`;
			// 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' + encodeURIComponent(item.keywords);
		}

		let rakutenBtn = '';
		if (rakutenLink) {
			rakutenBtn = `<a href="${rakutenLink}" class="button" rel="nofollow noreferrer" target="_blank">楽天商品ページを確認</a>`;
		}

		let info = '';
		if (item.info) {
			info = `<div className='pochipp-item__info'>ブランド：${item.info}</div>`;
		}

		let imageUrl = item.image_url;
		imageUrl = item.image_url;

		const searchedAt = item.searched_at;

		// 商品画像
		if (imageUrl) {
			if ('rakuten' === searchedAt) {
				imageUrl += '?_ex=100x100';
			}
			if ('amazon' === searchedAt) {
				imageUrl = imageUrl.replace('.jpg', '._SL100_.jpg');
			}
		}

		result += `<div class="pochipp-item" data-index="${index}" data-type="${type}">`;
		result += `
			<div class="pochipp-item__img">
				<img src="${imageUrl}" alt="" />
			</div>
			<div class="pochipp-item__body">
				<div class="pochipp-item__title">${item.title}</div>
				${info}
				<div class="pochipp-item__price">価格：¥${price.toLocaleString()}</div>
				<div class="pochipp-item__links"></div>`;

		// ボタン
		if ('registerd' === type) {
			const adminUrl = window.pochippIframeVars.adminUrl;
			const editUrl = `${adminUrl}post.php?post=${item.post_id}&action=edit`;

			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
				${amazonBtn}${rakutenBtn}
				<a class="button" data-pochipp="edit" href="${editUrl}" rel="nofollow noreferrer" target="_blank">この商品を編集</a>
			</div>`;
		} else {
			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
				${amazonBtn}${rakutenBtn}
			</div>`;
		}

		result += `</div></div>`;
	});

	return `<div class="pochipp-items">${result}</div>`;
};

(function ($) {
	// console.log('pochippIframeVars', window.pochippIframeVars);

	// 情報を取得
	const { ajaxUrl, tabKey, blockId, calledAt } = window.pochippIframeVars;

	// キーワード入力欄へフォーカスさせる
	const $keywords = $('#keywords');
	$keywords.focus();

	// フォームの送信イベント
	$('#search_form').submit(function (e) {
		e.preventDefault();

		// API検索の時はキーワード必須
		if ('pochipp_search_registerd' !== tabKey && $keywords.val() === '') {
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
		params.sort = $('#sort_select').val(); // 楽天 並び順
		params.term_id = $('#term_select').val(); // 商品カテゴリー
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
				// 描画するHTML
				let resultHtml = '';

				// 検索結果
				const searchedItems = datas.searched_items;

				// 取得済みデータ
				const registerdItems = datas.registerd_items;

				if ('editor' === calledAt) {
					// 投稿編集画面での呼び出し時のみ、「登録済み商品」を表示。
					const registerdHtml = getResultHtml(
						registerdItems,
						'registerd'
					);
					if (registerdHtml) {
						resultHtml +=
							'<div class="pchpp-tb__area-title">登録済み</div>';
						resultHtml += registerdHtml;
					}
				}

				// 普通の検索結果データを表示
				const searchedHtml = getResultHtml(searchedItems, 'searched');
				if (searchedHtml) {
					resultHtml +=
						'<div class="pchpp-tb__area-title">検索結果</div>';
					resultHtml += searchedHtml;
				}

				// console.log(searchedItems);
				// console.log(registerdItems);

				$('#result_area').html(resultHtml);

				// 「商品選択ボタン」のクリックイベントを登録
				$('[data-pochipp="select"]').click(function () {
					const $thisItem = $(this).parents('.pochipp-item');
					const itemIndex = $thisItem.attr('data-index');
					const itemtype = $thisItem.attr('data-type');
					const itemData =
						'registerd' === itemtype
							? registerdItems[itemIndex]
							: searchedItems[itemIndex];

					if ('editor' === calledAt) {
						window.top.set_block_data_at_editor(itemData, blockId);
					} else {
						window.top.setItemMetaData(itemData, false);
					}
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
