<?php
	ob_start();
?>
	
	<style type="text/css">
		#iamport-order-action .inside {padding: 0}
	</style>
	<div id="minor-publishing">
		<div class="misc-pub-section">
			<label for="iamport-order-status-meta"><?=__('결제상태 변경', 'iamport-payment')?> :</label>
			<select id="iamport-order-status-meta" name="new_iamport_order_status">
				<option value="ready" <?=$order_status=='ready'?'selected':''?>><?=__('미결제', 'iamport-payment')?></option>
				<option value="paid" <?=$order_status=='paid'?'selected':''?>><?=__('결제완료', 'iamport-payment')?></option>
				<option value="cancelled" <?=$order_status=='cancelled'?'selected':''?>><?=__('결제취소 및 환불처리', 'iamport-payment')?></option>
			</select>
		</div>
	</div>
	
	<div id="major-publishing-actions">
		<div id="delete-action"><?php
            if ( current_user_can( 'delete_post', $post->ID ) ) {

                if ( !EMPTY_TRASH_DAYS ) {
                        $delete_text = __('삭제하기', 'iamport-payment');
                } else {
                        $delete_text = __('휴지통으로 이동', 'iamport-payment');
                }
                ?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo $delete_text; ?></a><?php
            }
        ?></div>
        <div id="publishing-action">
        	<input type="submit" class="button save_order button-primary tips" name="save" value=<?=__("변경하기", 'iamport-payment')?> />
        </div>
        <div class="clear"></div>
	</div>

<?php
	return ob_get_clean();