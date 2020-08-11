<?php
	wp_register_style( 'iamport-order-view-css', plugins_url('../../assets/css/order-view.css', __FILE__));
	wp_enqueue_style('iamport-order-view-css');

	$buyer_name 	= $iamport_order->get_buyer_name();
	$buyer_email 	= $iamport_order->get_buyer_email();
	$buyer_tel 		= $iamport_order->get_buyer_tel();
	$shipping_addr = $iamport_order->get_shipping_addr();
	$extraFields 	= $iamport_order->get_extra_fields();
	$fileFields 	= $iamport_order->get_attached_files();

	ob_start();
?>

	<table class="iamport-order-view">
		<tbody>
			<tr>
				<th><?=__('주문번호', 'iamport-payment')?></th>
				<td><?=$iamport_order->get_order_uid()?></td>
			</tr>
			<?php if ($iamport_order->get_order_status(true) == 'paid') : ?>
			<tr>
				<th><?=__('매출전표', 'iamport-payment')?></th>
				<td><a href="<?=$iamport_order->get_receipt_url()?>" target="_blank"><?=__('매출전표보기', 'iamport-payment')?></a></td>
			</tr>
			<tr>
				<th><?=__('결제일자', 'iamport-payment')?></th>
				<td><?=$iamport_order->get_paid_date()?></td>
			</tr>
			<tr>
				<th><?=__('결제금액', 'iamport-payment')?></th>
				<td><?=$iamport_order->get_order_amount(true)?> <?=$iamport_order->get_amount_label()?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th><?=__('주문명', 'iamport-payment')?></th>
				<td><?=$iamport_order->get_order_title()?></td>
			</tr>
			<tr>
				<th><?=__('결제수단', 'iamport-payment')?></th>
				<td><?=$iamport_order->get_pay_method()?></td>
			</tr>
			<tr>
				<th><?=__('결제상태', 'iamport-payment')?></th>
				<td>
					<b><?=$iamport_order->get_order_status()?></b>
					<?php if ($iamport_order->get_order_status(true) == "failed") : 
						$failed_history = $iamport_order->get_failed_history();

						if (count($failed_history) > 0) :
						?>
						<br/>
						<i><?=$failed_history[0]["reason"]?></i>
					<?php endif; endif; ?>
				</td>
			</tr>
			<?php if ($iamport_order->get_pay_method(true) == 'vbank') : $vbank_info = $iamport_order->get_vbank_info(); ?>
				<?php if (!empty($vbank_info['name'])) : ?>
				<tr>
					<th><?=__('가상계좌 입금은행', 'iamport-payment')?></th>
					<td><?=$vbank_info['name']?></td>
				</tr>
				<?php endif; ?>
				<?php if (!empty($vbank_info['account'])) : ?>
				<tr>
					<th><?=__('가상계좌번호', 'iamport-payment')?></th>
					<td><?=$vbank_info['account']?></td>
				</tr>
				<?php endif; ?>
				<?php if (!empty($vbank_info['due'])) : ?>
				<tr>
					<th><?=__('가상계좌 입금기한', 'iamport-payment')?></th>
					<td><?=is_numeric($vbank_info['due']) ? date('Y-m-d H:i:s', $vbank_info['due']+(get_option( 'gmt_offset' ) * HOUR_IN_SECONDS)) : $vbank_info['due']?></td>
				</tr>
				<?php endif; ?>
			<?php endif; ?>
			<?php if(!empty($buyer_name)) : ?>
			<tr>
				<th><?=__('결제자 이름', 'iamport-payment')?></th>
				<td><?=$buyer_name?></td>
			</tr>
			<?php endif; ?>
			<?php if(!empty($buyer_email)) : ?>
			<tr>
				<th><?=__('결제자 Email', 'iamport-payment')?></th>
				<td><?=$buyer_email?></td>
			</tr>
			<?php endif; ?>
			<?php if(!empty($buyer_tel)) : ?>
			<tr>
				<th><?=__('결제자 전화번호', 'iamport-payment')?></th>
				<td><?=$buyer_tel?></td>
			</tr>
			<?php endif; ?>
			<?php if(!empty($shipping_addr)) : ?>
			<tr>
				<th><?=__('배송주소', 'iamport-payment')?></th>
				<td><?=$shipping_addr?></td>
			</tr>
			<?php endif; ?>
			
			<?php if(!empty($fileFields)) : foreach($fileFields as $file=>$fileData) : ?>
				<tr>
					<th><?=preg_replace('/%0D%0A/', '', $file)?></th>
					<td><a href='<?=$fileData['location']?>' download><?=$fileData['name']?></a></td>
				</tr>
			<?php endforeach; endif;?>
			<?php if( $extraFields) : foreach(json_decode($extraFields) as $name=>$field) : ?>
				<tr>
					<th><?=$name?></th>
					<td><?=$field?></td>
				</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>

<?php
	return ob_get_clean();
