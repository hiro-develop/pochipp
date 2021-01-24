/* eslint no-undef: 0 */
/* eslint no-alert: 0 */
/* eslint no-console: 0 */

/**
 * JSONのパース
 *
 * @param {string} data メタデータ(JSON形式)
 * @return {Array} parsed 配列に変換したメタデータ
 */
export const getParsedMeta = (data) => {
	try {
		const parsed = JSON.parse(data);
		return parsed;
	} catch (ex) {
		return {};
	}
};

/**
 * エディター下の「カスタムフィールド」の値を強制的にセットする処理
 */
export const setCustomFieldArea = (metaKey, metaVal) => {
	const customField = document.querySelector('#postcustomstuff');
	if (null === customField) return;

	const keyInput = customField.querySelector(`input[value="${metaKey}"]`);
	if (null === keyInput) return;

	const nextTd = keyInput.parentNode.nextElementSibling;
	if (null === nextTd) return;

	const textarea = nextTd.querySelector('textarea');
	if (null === textarea) return;

	textarea.value = metaVal;
};

/**
 * ajax実行処理
 */
export const sendUpdateAjax = async (params, doneFunc, failFunc) => {
	if (window.pchppVars === undefined) return;

	const ajaxUrl = window.pchppVars.ajaxUrl;
	const ajaxNonce = window.pchppVars.ajaxNonce;

	// nonce 追加
	params.append('nonce', ajaxNonce);

	await fetch(ajaxUrl, {
		method: 'POST',
		cache: 'no-cache',
		body: params,
	})
		.then((response) => {
			if (response.ok) {
				// console.log などで一回 response.json() 確認で使うと、responseJSONでbodyがlockされるので注意
				// console.log(response);
				return response.json();
			}
			throw new TypeError('Failed ajax!');
		})
		.then((json) => {
			doneFunc(json);
		})
		.catch((error) => {
			failFunc(error);
		});
};
