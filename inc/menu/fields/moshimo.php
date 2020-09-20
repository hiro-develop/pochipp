<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

// DB名
$db = \POCHIPP::DB_NAME;
?>
<h3 class="pcpp-setting__h3">もしもリンクの優先度</h3>
<?php
	\POCHIPP::output_checkbox([
		'key'   => 'is_use_moshimo_amazon',
		'label' => 'Amazonはもしもリンクを優先する',
	]);

	\POCHIPP::output_checkbox([
		'key'   => 'is_use_moshimo_rakuten',
		'label' => '楽天はもしもリンクを優先する',
	]);
?>
