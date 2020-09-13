<?php 
namespace POCHIPP;
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register taxnomy
 */
add_action( 'init', 'POCHIPP\register_tax_pochipp');
function register_tax_pochipp() {
	$tax_name = '商品カテゴリー';
	register_taxonomy(
		\POCHIPP::TAXONOMY_SLUG,
		[ \POCHIPP::POST_TYPE_SLUG ],
		[
			'label' => $tax_name,
			'labels' => [
				'popular_items' => $tax_name,
				'edit_item' => $tax_name . 'を編集',
				'add_new_item' => '新規' . $tax_name . 'を追加',
				'search_items' => $tax_name . 'を検索',
			],
			'public' => false,
			'show_ui' => true,
			'hierarchical' => true, // ??? true ? false ?
		]
	);
}
