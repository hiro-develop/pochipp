/**
 * apiFetch で meta取得 -> そんなことしなくてよかった
 */
apiFetch({
	path: '/pochipp/data',
	method: 'POST',
	data: {
		pid: 69,
	},
}).then((posts) => {
	console.log('pochipp', posts);
});

/**
 * apiFetchしなくても、getEntityRecord で商品投稿タイプからメタ情報取れる
 */
let pMetaJson = '{}';
if (pid) {
	pMetaJson = useSelect(
		(select) => {
			const data = select('core').getEntityRecord('postType', 'pochipps', pid);
			if (!data) return '{}';
			return data.meta?.pochipp_data || '{}';
		},
		[pid]
	);
}
const pMetaObj = useMemo(() => {
	return getParsedMeta(pMetaJson);
}, [pMetaJson]);
const pAttrs = { ...pMetaObj, ...attributes };
