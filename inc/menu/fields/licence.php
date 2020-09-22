<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

?>
<h3 class="pcpp-setting__h3">有料版ライセンス</h3>
<?php
	\POCHIPP::output_text_field([
		'key'   => 'pochipp_licence_key',
		'label' => 'ライセンスキー',
	]);
?>
<div class="pcpp-licence-status">
	<?php if ( \POCHIPP::check_licence() ) : ?>
		<p>ライセンスを確認できました。</P>
	<?php else : ?>
		<p>ライセンスが確認できません。</P>
	<?php endif; ?>
</div>
