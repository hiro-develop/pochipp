/**
 * thickboxで呼び出される iframe の中で読み込むスクリプト
 */
console.log('search.js!');

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
		result += `<div class="pochipp-items" data-index="${index}" data-type="${type}">`;
		result += `
                <div class="pochipp-items__img">
                    <img src="${item.s_image_url}" alt="" />
                </div>
                <div class="pochipp-items__title">${item.title}</div>
            `;
		if ('registerd' === type) {
			result += `<div class="pochipp-items__btns">
                <button classs="button" data-pochipp="select">この商品を選択</button>
                <button classs="button" data-pochipp="edit">この商品を編集</button>
            </div>`;
		} else {
			result += `<div class="pochipp-items__btns">
                <button classs="button" data-pochipp="select">この商品を選択</button>
            </div>`;
		}

		result += `</div>`;
	});

	return result;
};

(function ($) {
	// キーワード入力欄へフォーカスさせる
	$('#keywords').focus();

	const form = $('#search_form');

	// フォームの送信イベント
	form.submit(function (e) {
		e.preventDefault();

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
				const searchedItems = datas.api_datas;

				// 取得済みデータ
				const registerdItems = datas.old_datas;

				console.log('searchedItems', searchedItems);
				console.log('registerdItems', registerdItems);

				let resultHtml = '<div>登録済み</div>';
				resultHtml += getResultHtml(registerdItems, 'registerd');
				resultHtml += '<br><br><div>検索結果</div>';
				resultHtml += getResultHtml(searchedItems, 'searched');

				$('#result_area').html(resultHtml);

				// 商品選択時のイベントを登録
				$('[data-pochipp="select"]').click(function (e) {
					const $thisItem = $(this).parents('.pochipp-items');
					const itemIndex = $thisItem.attr('data-index');
					const itemtype = $thisItem.attr('data-type');
					const itemData =
						'registerd' === itemtype
							? registerdItems[itemIndex]
							: searchedItems[itemIndex];

					console.log('return:', itemData);

					window.top.set_block_data(itemData, blockid);
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
