<?php
  wp_register_style('iamport-migration-css', plugins_url('../../assets/css/iamport-migration.css', __FILE__), array(), "20180730");
  wp_enqueue_style('iamport-migration-css');

  require_once(dirname(__FILE__).'/../../model/migration/iamport-shortcode.php');

  global $wpdb;

  // 각 포스트의 마이그레이션 버튼 눌렀을때
	if ( isset($_POST['action']) && $_POST['action'] === "migrate_to_iamport_block") {
    $postId = $_POST['post_id'];
    $postContent = get_post($postId)->post_content;

    while(1) {
      // 숏코드 찾기
      preg_match(
        '/' . get_shortcode_regex() . '/s',
        $postContent,
        $matches,
        PREG_OFFSET_CAPTURE
      );

      if (count($matches) == 0) {
        break;
      }

      // 찾아진 숏코드
      $eachShortcode = $matches[0][0];
      // 숏코드의 위치
      $startAt = $matches[0][1];

      // 앞 스트링: 처음 ~ 숏코드 이전까지의 스트링
      $frontString = substr($postContent, 0, $startAt);
      // 뒷 스트링: 숏코드 이후 ~ 끝까지의 스트링
      $rearString = substr($postContent, $startAt + strlen($eachShortcode));

      // 각 숏코드 파싱
      $atts = shortcode_parse_atts($matches[3][0]);
      $content = $matches[5][0];
      $shortcode = new IamportShortcode($atts, $content);

      // 새 스트링: 앞 스트링 + 파싱된 숏코드 + 뒷 스트링
      $postContent =
        $frontString .
        '<!-- wp:cgb/iamport-payment ' .
        $shortcode->convertToJsonString() .
        ' /-->'.
        $rearString;
    }

    // TODO: <!-- wp:paragraph --><p> </p><!-- /wp:paragraph --> 제거
    $postContent = str_replace(
      "<!-- wp:paragraph -->\n<p><!-- wp:cgb/iamport-payment",
      "<!-- wp:cgb/iamport-payment",
      $postContent
    );

    $postContent = str_replace(
      "/--></p>\n<!-- /wp:paragraph -->",
      "/-->",
      $postContent
    );

    wp_update_post(
      array(
        'ID' => $postId,
        'post_content' => $postContent,
      ),
      true
    );
  }

	ob_start();

	$settings = get_option('iamport_block_setting');
	if ( empty($settings) ) {
		/* -------------------- 설정파일 백업으로부터 복원 -------------------- */
		$iamportSetting['user_code'] = get_option('iamport_user_code');
		$iamportSetting['rest_key'] = get_option('iamport_rest_key');
		$iamportSetting['rest_secret'] = get_option('iamport_rest_secret');
		$iamportSetting['login_required'] = get_option('iamport_login_required');
		$iamportSetting['biz_num'] = get_option('iamport_biz_num');

		update_option('iamport_block_setting', $iamportSetting);
	}
	$iamportSetting = get_option('iamport_block_setting');
?>
	<div class="iamport-block-container migration">
    <h1>아임포트 마이그레이션</h1>
    <div class="iamport-block-box">
      <h2><span>STEP1</span>아임포트 블록 플러그인이란?</h2>
      <p>
        아임포트 결제버튼 생성 플러그인은 <code>숏코드</code>를 기반으로 구성되어 있습니다. 하지만 숏코드를 작성하는게 다소 복잡하고, 다양한 기능을 제공하는데 있어 한계가 있었습니다. 워드프레스는 이러한 숏코드의 단점을 보완하기 위해 5.0 버전부터 <code>블록</code>이라는 개념을 도입했고 아임포트도 이에 발맞춰 새로운 블록 플러그인, <code>아임포트 블록 플러그인</code>을 개발했습니다.
      </p>
      <p>
        <code>아임포트 블록 플러그인</code>은 아임포트 결제버튼 생성 플러그인이 제공하는 모든 기능을 제공합니다. 다만 결제 팝업 속성을 지정함에 있어 훨씬 수월하고, 향후 신용카드/휴대폰 본인인증 및 정기결제 기능을 지원할 예정이니 아래 과정을 통해 새 플러그인으로 마이그레이션 하시길 바랍니다. <code>아임포트 블록 플러그인</code> 사용을 위한 워드프레스 최소 버전은 <code>5.2.3</code> 입니다.
      </p>
    </div>

    <div class="iamport-block-box">
      <h2><span>STEP2</span>아임포트 블록 플러그인 추가</h2>
      <p class="iamport-block-left">
        왼쪽 네비게이션 메뉴에서 <code>플러그인</code>을 클릭합니다. 플러그인 페이지에서 상단의 <code>새로추가</code> 버튼을 클릭합니다. 플러그인 추가 페이지에서 플러그인 검색 창에 <code>아임포트</code>를 입력하고 엔터를 누릅니다. 아임포트 플러그인 목록 중 <code>아임포트 블록 플러그인</code>의 <code>지금 설치</code>버튼을 클릭하면 설치가 완료됩니다.
      </p>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/install-plugin.png" alt="아임포트 블록 설치"/>
      </div>
      <p class="iamport-block-left">
        설치가 완료되면 네비게이션 메뉴에 <code>아임포트 결제내역</code>이 추가되고 하위 메뉴로 <code>아임포트 설정</code>, <code>아임포트 블록 매뉴얼</code>이 자동으로 셋팅됩니다. 각 메뉴의 역할은 기존의 아임포트 숏코드 플러그인과 동일합니다.
      </p>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/navigation.png" alt="아임포트 블록 네비게이션 메뉴"/>
      </div>
      <div class="iamport-block-clear"></div>
    </div>

    <div class="iamport-block-box">
      <h2><span>STEP3</span>아임포트 결제내역 마이그레이션</h2>
      <div class="iamport-block-left">
        <p>
          기존에 아임포트 숏코드 플러그인으로 결제된 내역을, 새 블록 플러그인으로 마이그레이션 한 후에도 확인해보실 수 있습니다. 이를 위해서는 기존의 결제내역을 <code>마이그레이션(복사)</code> 하셔야 합니다.
        </p>
        <p>
          결제내역 복사를 위해 기존의 숏코드 플러그인의 결제내역(왼쪽 내비게이션 매뉴 <code>아임포트 결제목록</code> 클릭)으로 이동합니다. 상단에 <code>결제내역 일괄 복사</code>라는 버튼이 생긴 것을 확인해보실 수 있습니다.
        </p>
      </div>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/copy-whole-lists.png" alt="결제내역 일괄 복사"/>
      </div>
      <p class="iamport-block-left">
        <code>결제내역 일괄 복사</code> 버튼을 누르면 총 3건의 결제목록(주문번호 <code>07295721_5f211dd12951b</code>, <code>07295453_5f211d3d60e79</code>, <code>07295045_5f211c4581c19</code>)이 모두 새 블록 플러그인의 결제내역으로 이동한 것을 확인해보실 수 있습니다. 만약 총 100건의 결제내역이 존재했다면, 100건이 모두 한번에 복사됩니다.
      </p>
      <div class="iamport-block-left">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/copy-whole-lists-result-1.png" alt="결제내역 일괄 복사 결과 1"/>
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/copy-whole-lists-result-2.png" alt="결제내역 일괄 복사 결과 2"/>
      </div>
      <p class="iamport-block-left">
        한꺼번에 모든 결제내역을 복사하는 것이 아닌, 원하는 결제내역만 복사할 수도 있습니다. 복사를 원하는 결제건을 선택(체크박스)하고, 일괄 작업 메뉴에서 <code>아임포트 결제내역 복사</code>를 선택해 <code>적용</code> 버튼을 눌러주세요.
      </p>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/copy-lists.png" alt="결제내역 복사"/>
      </div>
      <p class="iamport-block-left">
        복사 된 결제내역을 다시 복구할 수도 있습니다. 새 블록 플러그인의 결제내역으로 이동해, <code>결제내역 일괄 복구</code> 버튼을 눌러주세요.
      </p>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/rollback-whole-lists.png" alt="결제내역 일괄 복구"/>
      </div>
      <p class="iamport-block-left">
        <code>결제내역 일괄 복구</code> 버튼을 누르면 총 3건의 결제내역(주문번호 <code>07295721_5f211dd12951b</code>, <code>07295453_5f211d3d60e79</code>, <code>07295045_5f211c4581c19</code>)이 다시 아임포트 숏코드 플러그인 결제목록으로 이동한 것을 확인해보실 수 있습니다.
      </p>
      <div class="iamport-block-left">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/rollback-whole-lists-result-1.png" alt="결제내역 일괄 복구 결과 1"/>
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/rollback-whole-lists-result-2.png" alt="결제내역 일괄 복구 결과 2"/>
      </div>
      <p class="iamport-block-left">
        한꺼번에 모든 결제내역을 복구하는 것이 아닌, 원하는 결제내역만 복구할 수도 있습니다. 복구를 원하는 결제건을 선택(체크박스)하고, 일괄 작업 메뉴에서 <code>아임포트 결제내역 복구</code>를 선택해 <code>적용</code> 버튼을 눌러주세요.
      </p>
      <div class="iamport-block-left iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/rollback-lists.png" alt="결제내역 복구"/>
      </div>
      <div class="iamport-block-clear"></div>
    </div>

    <div class="iamport-block-box">
      <h2><span>STEP4</span>아임포트 숏코드 마이그레이션</h2>
      <p>
        아래는 숏코드가 포함된 포스트의 리스트입니다. 각 포스트에 대해 마이그레이션 버튼을 눌러, 기존의 숏코드를 새 블록으로 대체할 수 있습니다. 
      </p>
      <table>
        <thead>
          <tr>
            <th>포스트 ID</th>
            <th>포스트 타이틀</th>
            <th>포스트 컨텐츠</th>
            <th>블록으로 전환</td>
          </tr>
        </thead>
        <tbody>
          <?php
            // 워드프레스에 설정된 페이지/포스트 중 iamport_payment_button 키워드를 포함하고 있는 포스트를 가져온다
            $posts = $wpdb->get_results(
              $wpdb->prepare(
                "SELECT * FROM {$wpdb->posts} WHERE post_status = %s AND (post_type = %s OR post_type = %s) AND post_content LIKE '%iamport_payment_button%'",
                "publish",
                "page",
                "post"
              )
            );

            foreach($posts as $post) {
              // 숏코드를 포함하고 있는 포스트의 리스트만 렌더링한다
              preg_match(
                '/\[iamport_payment_button(.*)\[\/iamport_payment_button\]/',
                $post->post_content,
                $matches,
                PREG_OFFSET_CAPTURE
              );
              if (count($matches) > 0) {
                ?>
                  <tr>
                    <td><a href="<?=home_url().'/'.$post->post_name?>" target="_blank"><?=$post->ID?></a></td>
                    <td><?=$post->post_title?></td>
                    <td><textarea><?=$post->post_content?></textarea></td>
                    <td>
                      <form method="post" action="">
                        <input type="hidden" name="post_id" value="<?=$post->ID?>" />
                        <input type="hidden" name="action" value="migrate_to_iamport_block" />
                        <input class="button-primary" type="submit" name="iamport-options" value="마이그레이션" />
                      </form>
                    </td>
                  </tr>
                <?php
              }
            }
            wp_reset_postdata();
            
            if (count($posts) === 0) { ?>
              <tr>
                <td colspan="4"><p>마이그레이션이 필요한 포스트가 없습니다</p></td>
              </tr><?php
            } ?>
        </tbody>
      </table>
    </div>
    <div class="iamport-block-box">
      <h2><span>STEP5</span>아임포트 숏코드 플러그인 삭제</h2>
      <p>
        이제 <code>아임포트 블록 플러그인</code> 사용을 위한 모든 작업이 끝났습니다. 아임포드 결제버튼 생성 플러그인(<code>iamport-payment</code>)을 삭제(또는 비활성화) 하실 수 있습니다.
      </p>
      <div class="iamport-text-center">
        <img src="<?=plugin_dir_url( __FILE__ )?>../../assets/img/uninstall-plugin.png" alt="플러그인 삭제"/>
      </div>
    </div>
	</div>
<?php
	$iamport_admin_html = ob_get_clean();
	return $iamport_admin_html;
