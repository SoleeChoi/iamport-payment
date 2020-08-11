<?php
	wp_register_style( 'iamport-order-list-css', plugins_url('../../assets/css/order-list.css', __FILE__));
	wp_enqueue_style('iamport-order-list-css');

	ob_start();
?>

	<table class="iamport-order-list">
		<thead>
			<tr>
				<th class="column-primary"><?=__('주문명', 'iamport-payment')?></th>
				<th><?=__('주문번호', 'iamport-payment')?></th>
				<th><?=__('결제수단', 'iamport-payment')?></th>
				<th><?=__('주문일자', 'iamport-payment')?></th>
				<th><?=__('주문상태', 'iamport-payment')?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($posts as $p) : $iamport_order = new IamportOrder($p); ?>
			<tr>
				<td class="column-primary">
					<?=$p->post_title?>
					<span class="mobile-content">
						<br><span><b><?=__('주문번호', 'iamport-payment')?></b> : <?=$iamport_order->get_order_uid()?></span>
						<br><span><b><?=__('결제수단', 'iamport-payment')?></b> : <?=$iamport_order->get_pay_method()?></span>
						<br><span><b><?=__('주문일자', 'iamport-payment')?></b> : <?=$iamport_order->get_paid_date()?></span>
						<br><span><b><?=__('주문상태', 'iamport-payment')?></b> : <?=$iamport_order->get_order_status()?></span>
						<br><a target="_blank" class="view-order" href="<?=add_query_arg( 'iamport-order-view', $iamport_order->get_order_uid(), $history_page_url )?>"><?=__('보기', 'iamport-payment')?></a>
					</span>
				</td>
				<td><?=$iamport_order->get_order_uid()?></td>
				<td><?=$iamport_order->get_pay_method()?></td>
				<td><?=$iamport_order->get_paid_date()?></td>
				<td><?=$iamport_order->get_order_status()?></td>
				<td><a target="_blank" class="view-order" href="<?=add_query_arg( 'iamport-order-view', $iamport_order->get_order_uid(), $history_page_url )?>"><?=__('보기', 'iamport-payment')?></a></td>
			</tr>
			<?php endforeach; ?>

			<?php if (empty($posts)) : ?>
			<tr>
				<td colspan="6"><?=__('결제내역을 찾을 수 없습니다.', 'iamport-payment')?></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
<?php
	return ob_get_clean();
