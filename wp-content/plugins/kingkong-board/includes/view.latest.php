<?php
/**
 * 킹콩보드 최신글 리스트 HTML 
 * 
 * $attr     = 숏코드 파라미터 값
 * $board_id = 게시판 아이디
 * $latests  = 최신글 리스트
 */
?>
<div id="kingkongboard-latest-wrapper">
  <table id="kingkongboard-latest-table" summary="<?php _e('제목, 작성일 정보 제공', 'kingkongboard');?>">
    <caption class="blind"><?php echo $summary;?></caption>
    <thead>
      <tr>
        <th><?php _e('제목', 'kingkongboard');?></th>
        <th style="text-align:center; width:100px"><?php _e('작성일', 'kingkongboard');?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $count = 1;
  foreach($latests as $latest){
?>
    <tr>
<?php
      foreach($latest_priority as $priority){
        switch($priority){
          case "title" :
            $read_path = add_query_arg(array('view' => 'read', 'id' => $latest->post_id), get_the_permalink($latest->guid));
            $title     = get_the_title($latest->post_id);
            $title     = str_replace('Private: ', '', $title);
            $title     = str_replace('비공개: ', '', $title);
            $title = kingkongboard_text_cut($title, $length, "...");
?>
      <td class="kingkongboard-latest-td-<?php echo $priority;?>">
        <a href="<?php echo $read_path;?>"><?php echo apply_filters('kkb_latest_title', $title, $board_id, $latest->post_id);?></a>
      </td>
<?php
          break;

          case "date" :
?>
      <td class="kingkongboard-latest-td-<?php echo $priority;?>">
        <?php echo get_the_date('Y-m-d', $latest->post_id); ?>
      </td>
<?php
          break;
        }
      }
?>
    </tr>
<?php
    $count++;
  }
?>
    </tbody>
  </table>
</div>