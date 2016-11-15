<?php
/**
 * 킹콩보드 데이터 백업 및 복구
 * @link www.superrocket.io
 * @copyright Copyright 2015 SuperRocket. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl.html
 */
  class kkb_Backup {
    function __construct(){
      // 데이터 내보내기시 킹콩보드의 메타 데이터 와 코멘트 메타 데이터를 포스트메타에 삽입한다.
      add_action( 'export_wp', array($this, 'kkb_export_array'), 10, 1 );

      // 데이터 가져오기시 포스트를 집어넣을때 킹콩보드 메타 데이터 값을 다시 설정한다.
      add_action( 'wp_import_insert_post', array($this, 'kkb_import_post'), 10, 4);

      // 데이터 가져오기시 포스트메타 값을 넣을때 킹콩보드의 메타 데이터 값을 킹콩보드 테이블로 삽입한다.
      add_action( 'import_post_meta', array($this, 'kkb_import_array'), 10, 3);

      // 데이터 가져오기시 코멘트메타 값을 넣을때 킹콩보드의 코멘트 메타 데이터 값을 테이블로 삽입한다.
      add_action( 'wp_import_insert_comment', array($this, 'kkb_import_comment_array'), 10, 4);

      // 모든 가져오기가 완료되는 시점에 다시한번 킹콩보드 복구 데이터를 정상화 한다.
      add_action( 'import_end', array($this, 'kkb_import_end') );

      // 임포트할 킹콩보드의 포스트타입이 없다면 등록한다.
      add_filter( 'wp_import_post_data_raw', array($this, 'kkb_import_register_post_type'), 10, 1 );      
    }


    public function kkb_import_register_post_type( $post ){
      if ( !post_type_exists( $post['post_type'] ) ) {
        register_post_type( $post['post_type'] );
      }
      return $post;
    }

    public function kkb_import_end(){
      global $wpdb;
      $meta_table     = $wpdb->prefix.'kingkongboard_meta';
      $comment_table  = $wpdb->prefix.'kingkongboard_comment_meta';
      $change_ids     = get_option('kkb_change_id', true);
      $change_ids     = maybe_unserialize($change_ids);

      if($change_ids){
        foreach($change_ids as $change_id){
          $new_id           = $change_id['new_id'];
          $post_type        = get_post_type($new_id);

          if(preg_match("kkboard", $post_type)){
            $board_slug       = "kkb_".get_post_meta($new_id, 'kingkongboard_slug', true);
            $added_page       = check_board_shortcode_using( "kingkong_board ".$board_slug );
            $added_page_id    = $added_page[0];
            
            $wpdb->update(
              $meta_table,
              array( 'guid' => $change_id['new_id'] ),
              array( 'guid' => $change_id['original_id'] ),
              array( '%d' ),
              array( '%d' )
            );  
          }

          $wpdb->update(
            $comment_table,
            array( 'eid' => $change_id['new_id'] ),
            array( 'eid' => $change_id['original_id'] ),
            array( '%d' ),
            array( '%d' )
          );          

          $wpdb->update(
            $meta_table,
            array( 'board_id' => $change_id['new_id'] ),
            array( 'board_id' => $change_id['original_id'] ),
            array( '%d' ),
            array( '%d' )
          );

          $wpdb->update(
            $meta_table,
            array( 'post_id' => $change_id['new_id'] ),
            array( 'post_id' => $change_id['original_id'] ),
            array( '%d' ),
            array( '%d' )
          );

          $wpdb->update(
            $meta_table,
            array( 'related_id' => $change_id['new_id'] ),
            array( 'related_id' => $change_id['original_id'] ),
            array( '%d' ),
            array( '%d' )
          );

          $wpdb->update(
            $meta_table,
            array( 'parent' => $change_id['new_id'] ),
            array( 'parent' => $change_id['original_id'] ),
            array( '%d' ),
            array( '%d' )
          );

        }
      }
      // 중복 레코드를 제거
      $wpdb->query("DELETE FROM {$meta_table} WHERE ID NOT IN ( SELECT ID FROM ( SELECT ID FROM {$meta_table} GROUP BY post_id ) as b )");
      $wpdb->query("DELETE FROM {$comment_table} WHERE ID NOT IN ( SELECT ID FROM ( SELECT ID FROM {$comment_table} GROUP BY cid ) as b )");
    }

    public function kkb_import_post( $post_id, $original_post_ID, $postdata, $post ){
      if(preg_match("kkb_", $post['post_type']) || preg_match("kkboard", $post['post_type'])){
        $change_id   = get_option('kkb_change_id', true);
        $change_id[] = array(
          'original_id' => $original_post_ID,
          'new_id'      => $post_id
        );
        update_option('kkb_change_id', $change_id);
      }
    }

    public function kkb_import_comment_array( $comment_id, $comment, $comment_post_ID, $post ){
      global $wpdb;
      $table  = $wpdb->prefix.'kingkongboard_comment_meta';
      $change_comment_id = null;
      foreach( $comment['commentmeta'] as $meta ) {
        $value = maybe_unserialize( $meta['value'] );
        if( $meta['key'] == 'kkb_export_comment_meta' ){
            $status = $wpdb->insert(
              $table,
              array(
                'lnumber'  => $value['lnumber'],
                'eid'      => $value['eid'],
                'cid'      => $comment_id,
                'origin'   => $value['origin'],
                'parent'   => $value['parent'],
                'depth'    => $value['depth']
              ),
              array( '%d', '%d', '%d', '%d', '%d', '%d' )
            );
            if(!is_wp_error($status)){
              $change_comment_id = array(
                'original_id' => $value['cid'],
                'new_id'      => $comment_id
              );
            }
        }
      }
      
    }

    public function kkb_import_array( $post_id, $key, $value ){
      global $wpdb;
      if($key == 'kkb_export_meta'){
          $table      = $wpdb->prefix.'kingkongboard_meta';
          $status     = $wpdb->insert(
            $table,
            array(
              'board_id'    => $value['board_id'],
              'post_id'     => $value['post_id'],
              'section'     => $value['section'],
              'related_id'  => $value['related_id'],
              'list_number' => $value['list_number'],
              'depth'       => $value['depth'],
              'parent'      => $value['parent'],
              'type'        => $value['type'],
              'date'        => $value['date'],
              'guid'        => $value['guid'],
              'login_id'    => $value['login_id'],
              'writer'      => $value['writer']
            ), 
            array( '%d','%d','%s','%d','%d','%d','%d','%d','%d','%d','%d','%s')
          );
      }
    }

    public function kkb_export_array($args){
      global $wpdb;
      $type = $args['content'];

        $config = new kkbConfig();
        
        $meta_table    = $wpdb->prefix.'kingkongboard_meta';
        $comment_table = $wpdb->prefix.'kingkongboard_comment_meta';

        $metas         = $wpdb->get_results("SELECT * FROM {$meta_table}");
        $comments      = $wpdb->get_results("SELECT * FROM {$comment_table}");

        // kingkongboard_meta
        if($metas){
          foreach($metas as $value){
            $board  = $config->getBoard($value->board_id);
            $meta_array = array(
              'board_slug' => $board->slug,
              'board_id'   => $value->board_id,
              'post_id'    => $value->post_id,
              'section'    => $value->section,
              'related_id' => $value->related_id,
              'list_number'=> $value->list_number,
              'depth'      => $value->depth,
              'parent'     => $value->parent,
              'type'       => $value->type,
              'date'       => $value->date,
              'guid'       => $value->guid,
              'login_id'   => $value->login_id,
              'writer'     => $value->writer
            );
            update_post_meta($value->post_id, 'kkb_export_meta', $meta_array);
          }
        }

        // kingkongboard_comment_meta
        if($comments){
          foreach($comments as $comment){
            $comment_array = array(
              'lnumber' => $comment->lnumber,
              'eid'     => $comment->eid,
              'cid'     => $comment->cid,
              'origin'  => $comment->origin,
              'parent'  => $comment->parent,
              'depth'   => $comment->depth
            );
            update_comment_meta($comment->cid, 'kkb_export_comment_meta', $comment_array);
          }
        }

    }

    public function register_post_types(){
      global $wpdb;

      if( !post_type_exists( "kkboard" ) ){
        register_post_type( "kkboard",
            array(
                'labels' => array(
                    'name' => __('킹콩보드', 'kingkongboard')
                ),
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false
            )
        );
      }
      
      $table   = $wpdb->prefix."posts";
      $results = $wpdb->get_results("SELECT * FROM ".$table." WHERE post_type = 'kkboard' AND post_status = 'publish' ");
      if($results){
        foreach($results as $result){

          $board_id         = $result->ID;
          $board_slug       = get_post_meta($board_id, 'kingkongboard_slug', true);
          $board_secret     = get_post_meta($board_id, 'kingkongboard_search', true);
          ($board_secret == 'F') ? $public_value = false : $public_value = true;
          if($board_slug){
            register_post_type( 'kkb_'.$board_slug,
              array(
                'labels' => array(
                  'name' => get_the_title($board_id)
                ),
                'public' => $public_value,
                'show_ui' => false,
                'show_in_menu' => false,
                'capability_type' => 'kkb_'.$board_slug,
                'capabilities' => array(
                  'edit_post'           => 'edit_kkb_'.$board_slug,
                  'edit_posts'          => 'edit_kkb_'.$board_slug.'s',
                  'edit_others_posts'   => 'edit_others_kkb_'.$board_slug.'s',
                  'publish_posts'       => 'publish_kkb_'.$board_slug.'s',
                  'read_post'           => 'read_kkb_'.$board_slug,
                  'read_private_posts'  => 'read_private_kkb_'.$board_slug.'s',
                  'delete_post'         => 'delete_kkb_'.$board_slug,
                  'edit_comment'        => 'edit_comment_kkb_'.$board_slug,
                ),
                'map_meta_cap'    => true
                )
            );
          }
        }
      }      
    }
  }

  new kkb_Backup();
?>