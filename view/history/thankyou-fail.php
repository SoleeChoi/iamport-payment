<?php
	ob_start();
?>

	<h3><?=__('결제 정보를 확인할 수 없습니다.', 'iamport-payment')?></h3>
	<script type="text/javascript">
		location.href = '<?=$order_view_url?>';
	</script>
	
<?php
	return ob_get_clean();