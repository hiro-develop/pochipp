/**
 * thickboxで呼び出される iframe の中で読み込むスクリプト
 */

/**
 * 検索結果の商品リストをHTMLとして取得
 *
 * @param {Array} itemDatas 検索結果データ
 * @param {string} type 'registerd' or 'searched'
 */
const getItemList = (itemDatas, type) => {
	// console.log('itemDatas', itemDatas);

	// エラーが返ってきている場合
	if (itemDatas.error) {
		return `<div class="pochipp-items--errot">${itemDatas.error.code} : ${itemDatas.error.message}</div>`;
	}

	let result = '';
	Object.keys(itemDatas).forEach((index) => {
		const item = itemDatas[index];
		const price = Number(item.price);
		const searchedAt = item.searched_at;

		// 商品詳細ページ取得
		const amazonLink = item.asin
			? 'https://www.amazon.co.jp/dp/' + item.asin
			: '';
		const rakutenLink = item.rakuten_detail_url || '';

		const yahooLink = item.yahoo_detail_url || '';

		let amazonBtn = '';
		if (amazonLink) {
			amazonBtn = `<a href="${amazonLink}" class="button" rel="nofollow noopener noreferrer" target="_blank">Amazon商品ページを確認</a>`;
		}

		let rakutenBtn = '';
		if (rakutenLink) {
			rakutenBtn = `<a href="${rakutenLink}" class="button" rel="nofollow noopener noreferrer" target="_blank">楽天商品ページを確認</a>`;
		}

		let yahooBtn = '';
		if (yahooLink) {
			const yahooBtnText = item.is_paypay
				? 'PayPayモール商品ページを確認'
				: 'Yahooショッピング商品ページを確認';
			yahooBtn = `<a href="${yahooLink}" class="button" rel="nofollow noopener noreferrer" target="_blank">${yahooBtnText}</a>`;
			// yahooBtn += `/ ${item.seller_id} / ${item.yahoo_itemcode}`;
		}

		let info = '';
		if (item.info) {
			info = `<div class='pochipp-item__info'>${item.info}</div>`;
		}

		let imageUrl = item.image_url;
		// 商品画像
		if (imageUrl) {
			if ('rakuten' === searchedAt) {
				imageUrl += '?_ex=100x100';
			}
			if ('amazon' === searchedAt) {
				imageUrl = imageUrl.replace('.jpg', '._SL100_.jpg');
			}
		}

		// 商品情報
		result += `<div class="pochipp-item" data-index="${index}" data-type="${type}">
			<div class="pochipp-item__img">
				<img src="${imageUrl}" alt="" />
			</div>
			<div class="pochipp-item__body">
				<div class="pochipp-item__title">${item.title}</div>
				${info}
				<div class="pochipp-item__price">価格：¥${price.toLocaleString()}</div>
		`;

		// ボタン
		if ('registerd' === type) {
			const adminUrl = window.pochippIframeVars.adminUrl;
			const editUrl = `${adminUrl}post.php?post=${item.post_id}&action=edit`;

			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
				${amazonBtn}${rakutenBtn}${yahooBtn}
				<a class="button" data-pochipp="edit" href="${editUrl}" rel="nofollow noreferrer" target="_blank">この商品を編集</a>
			</div>`;
		} else {
			result += `<div class="pochipp-item__btns">
				<button class="button button-primary" data-pochipp="select">この商品を選択</button>
				${amazonBtn}${rakutenBtn}${yahooBtn}
			</div>`;
		}

		result += `</div></div>`;
	});

	return result;
};

/**
 * 検索結果のをHTMLとして取得
 */
const getResultHtml = (searchedItems, registerdItems, calledAt) => {
	// console.log(searchedItems);
	// console.log(registerdItems);

	let resultHtml = '';

	// 投稿編集画面での呼び出し時のみ、「登録済み商品」を表示。
	if ('editor' === calledAt) {
		const registerdList = getItemList(registerdItems, 'registerd');
		if (registerdList) {
			resultHtml += `<div class="pchpp-tb__area-title">登録済み商品</div><div class="pochipp-items">${registerdList}</div>`;
		}
	}

	// 普通の検索結果データを表示
	const searchedList = getItemList(searchedItems, 'searched');
	if (searchedList) {
		resultHtml += `<div class="pchpp-tb__area-title">検索結果</div><div class="pochipp-items">${searchedList}</div>`;
	}

	return resultHtml;
};

(function ($) {
	/**
	 * 商品検索のAjax実行部分
	 */
	const doSearchAjax = (params) => {
		const { ajaxUrl, blockId, calledAt, only } = window.pochippIframeVars;
		// console.log(window.pochippIframeVars);

		// 検索エリアの描画をリセット
		$('#result_area').html('');

		// paramsセット
		// params.search_index = $('#search_index').val(); // Amazonの商品カテゴリー
		// params.sort = $('#sort_select').val(); // 並び順 : 楽天 ＆ 登録済みタブで使用
		params.term_id = $('#term_select').val(); // 商品カテゴリー : 登録済みタブで使用
		params.only = only; // 追加の限定検索かどうか

		// params.page = 1;

		// nonceセット
		const ajaxNonce = window.pchppVars.ajaxNonce;
		params.nonce = ajaxNonce;

		// ajax実行
		$.ajax({
			url: ajaxUrl,
			dataType: 'json',
			data: params,
			beforeSend: () => {
				$('#loading_image').show(); // ローディング画像の表示開始
			},
		})
			.done(function (datas, textStatus, jqXHR) {
				// console.log('doSearchAjax: datas', datas);

				if (datas.error) {
					$('#result_area').html(
						`<p>${datas.error.code}: ${datas.error.message}</p>`
					);
					return;
				}

				// 検索結果
				const searchedItems = datas.searched_items;

				// 取得済みデータ
				const registerdItems = datas.registerd_items;

				// 結果のHTML
				const resultHtml = getResultHtml(
					searchedItems,
					registerdItems,
					calledAt
				);

				// HTMLを描画
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

					// 商品データ更新
					if (only) {
						const onlyData = {};
						if ('amazon' === only) {
							onlyData.asin = itemData.asin || '';
							onlyData.amazon_affi_url =
								itemData.amazon_affi_url || '';
						} else if ('rakuten' === only) {
							onlyData.itemcode = itemData.itemcode || '';
							onlyData.rakuten_detail_url =
								itemData.rakuten_detail_url || '';
						} else if ('yahoo' === only) {
							onlyData.yahoo_itemcode =
								itemData.yahoo_itemcode || '';
							onlyData.yahoo_detail_url =
								itemData.yahoo_detail_url || '';
							onlyData.seller_id = itemData.seller_id || '';
							onlyData.is_paypay = itemData.is_paypay || '';
						}

						if ('editor' === calledAt) {
							window.top.set_block_data_at_editor(
								onlyData,
								blockId
							);
						} else {
							window.top.setItemMetaData(onlyData, true);
						}
					} else if ('editor' === calledAt) {
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
	};

	/**
	 * メインスクリプト
	 */
	(function () {
		// console.log('pochippIframeVars', window.pochippIframeVars);

		// 情報を取得
		const { tabKey } = window.pochippIframeVars;

		// キーワード入力欄へフォーカスさせる
		const $keywords = $('#keywords');
		$keywords.focus();

		if ('pochipp_search_registerd' === tabKey) {
			const params = {
				action: tabKey,
				count: '5',
			};

			// 検索開始
			doSearchAjax(params);
		}

		// フォームの送信イベント
		$('#search_form').submit(function (e) {
			e.preventDefault();

			// ajaxアクション名 : タブキーがそのままアクション名となる
			const action = tabKey || '';
			if (!action) {
				$('#result_area').html(
					'<p>エラー : アクション名が不明です。</p>'
				);
				return;
			}

			// API検索かどうか
			const useAPI = 'pochipp_search_registerd' !== action;
			const keywords = $('#keywords').val();

			// API検索の時はキーワード必須
			if (useAPI && !keywords) {
				$('#result_area').html('<p>キーワードを入力して下さい。</p>');
				return;
			}

			// 検索開始
			const params = {
				action,
				keywords,
			};
			doSearchAjax(params);

			return false;
		});
	})();
})(window.jQuery);
