<?php
//Loading CSS
add_action( 'wp_enqueue_scripts', 'hover_effects_shortcode_css' );
function hover_effects_shortcode_css() {
    wp_enqueue_style('ahew_stylesheet', plugins_url( '/css/ihover.css' , __FILE__ ) );  
}

add_action('init', 'hover_effects_shortcode_init', 99 );
function hover_effects_shortcode_init(){
    global $kc;
    $kc->add_map(
        array(
            'hover_effects' => array(
                'name' => 'Hover Effects',
                'description' => __('Display hover effects on images', 'KingComposer'),
                'icon' => 'kc-icon-image',
                //'is_container' => true,
                'category' => 'KC RD Extensions',
                'css_box'    => true,
                'params' => array(

              'General' => array ( 
                    array(
                        'name' => 'hover_image',
                        'label' => 'Hover Image',
                        'type' => 'attach_image',
                    ),
                    array(
                        'name' => 'hover_heading',
                        'label' => 'Heading',
                        'type' => 'text',
                        'value' => __('Heading Here', 'KingComposer')
                    ),
                    array(
                        'name' => 'hov_desc',
                        'label' => 'Description',
                        'type' => 'textarea',
                        'value' => base64_encode('Description Goes Here'),
                    ),
                    array(
                        'name' => 'custom_link',
                        'label' => 'Custom Link?',
                        'type' => 'checkbox',
                        'options' => array(
                            'yes' => 'Yes',
                        ),
                    ),
                    array(
                        'name' => 'hover_link',
                        'label' => 'Image Link',
                        'type' => 'text',
                        'relation' => array(
                            'parent' => 'custom_link',
                            'show_when' => 'yes'
                            ),
                    ),
                    array(
                        'name' => 'link_new_tab',
                        'label' => 'Open Link in New Tab?',
                        'type' => 'checkbox',
                        'options' => array(
                            '_blank' => 'Yes',
                        ),                        
                        'relation' => array(
                            'parent' => 'custom_link',
                            'show_when' => 'yes'
                            ),
                    ),
                    array(
                        'name' => 'hover_style',
                        'label' => 'Select Style',
                        'type' => 'select',
                        //'value' => 'bg_img', // Set default is value_2, remove this if you dont need to set default                     
                        'options' => array(
                             'square' => 'Square',
                             'circle' => 'Circle',
                        ),
                    ),
                    array(
                        'name' => 'hover_effect',
                        'label' => 'Select Effect',
                        'type' => 'select',
                        //'value' => 'bg_img', // Set default is value_2, remove this if you dont need to set default
                        //'description' => 'My description',                        
                        'options' => array(
                             'effect1' => 'Effect 1',
                             'effect2' => 'Effect 2',
                             'effect3' => 'Effect 3',
                             'effect4' => 'Effect 4',
                             'effect5' => 'Effect 5',
                             'effect6' => 'Effect 6',
                             'effect7' => 'Effect 7',
                             'effect8' => 'Effect 8',
                             'effect9' => 'Effect 9',
                             'effect10' => 'Effect 10',
                             'effect11' => 'Effect 11',
                             'effect12' => 'Effect 12',
                             'effect13' => 'Effect 13',
                             'effect14' => 'Effect 14',
                             'effect15' => 'Effect 15',
                             'effect16' => 'Effect 16',
                             'effect17' => 'Effect 17',
                             'effect18' => 'Effect 18',
                             'effect19' => 'Effect 19',
                             'effect20' => 'Effect 20',
                        ),
                        'relation' => array(
                            'parent' => 'hover_style',
                            'show_when' => array( 'square', 'circle' ),
                            ),                        
                    ),
                    array(
                        'name' => 'hover_animation',
                        'label' => 'Animation Direction',
                        'type' => 'select',                    
                        'options' => array(
                             'left_to_right' => 'Left to Right',
                             'right_to_left' => 'Right to Left',
                             'top_to_bottom' => 'Top to Bottom',
                             'bottom_to_top' => 'Bottom to Top',
                        ),
                    ), 

                    array(
                        'name' => 'remove_border',
                        'label' => 'Remove Border?',
                        'type' => 'checkbox',
                        'options' => array(
                            'none' => 'Yes',
                        ),
                    ),                    
                    array(
                        'name' => 'remove_hor_line',
                        'label' => 'Remove Horizontal Line?',
                        'type' => 'checkbox',
                        'options' => array(
                            'none' => 'Yes',
                        ),
                    ),
                    ), 



'Typography' => array(
                                        array(
                                            'name' => 'heading_font_size',
                                            'label' => 'Heading Font Size',
                                            'type' => 'number_slider',
                                            'options' => array(
                                                'min' => 1,
                                                'max' => 50,
                                                'unit' => 'px',
                                                'show_input' => true
                                            ),
                                            'value' => 16,
                                            'description' => 'use custom heading font size, default is 16'
                                        ),
                                         array(
                                            'name' => 'desc_font_size',
                                            'label' => 'Description Font Size',
                                            'type' => 'number_slider',
                                            'options' => array(
                                                'min' => 1,
                                                'max' => 50,
                                                'unit' => 'px',
                                                'show_input' => true
                                            ),
                                            'value' => 12,
                                            'description' => 'use custom description font size, default is 12'
                                        ),
                                        array(
                                            'name' => 'heading_color',
                                            'label' => 'Heading Color',
                                            'type' => 'color_picker',
                                            'value' => '#F2F2F2',
                                            'description' => 'default color is #F2F2F2'
                                        ),                     
                                        array(
                                            'name' => 'description_color',
                                            'label' => 'Description Color',
                                            'type' => 'color_picker',
                                            'value' => '#F2F2F2',
                                            'description' => 'default color is #F2F2F2'
                                        ), 

                                       array(
                                            'name' => 'bg_color',
                                            'label' => 'Background Color',
                                            'type' => 'color_picker',
                                            'value' => '#333333',
                                            'description' => 'default color is #F2F2F2'
                                        ),
                        ),
                                       
                )
            )
        )
    );
}
// Register Hover Shortcode
function hover_image_shortcode($atts, $content = null){
    extract( shortcode_atts( array(
        'hover_image' => '',
        'hover_heading' => '',
        'hov_desc' => '',
        'custom_link' => '',
        'hover_link' => '',
        'link_new_tab' => '',
        'hover_style' => '',
        'hover_effect' => '',
        'hover_animation' => '',
        'remove_border' => '8px solid #ffffff;',
        'remove_hor_line' => '',
        'heading_font_size' => '',
        'heading_color' => '',
        'desc_font_size' => '',
        'description_color' => '',
        'link' => '',
        'bg_color' => '',
        'css' => '',
    ), $atts) );
    $hover_image = wp_get_attachment_image_src( $hover_image,'full' );
   //  $hov_desc = base64_decode( $atts['hov_desc'] );
           echo ' <style type="text/css">
                .kov-items.circle.'.$hover_effect.' .info h3 {
                  height: 84px;
                  letter-spacing: 1px;
                  margin: 0 20px;
                  padding: 47px 0 0;
                }
             </style>';
    if ( $hover_style=='circle' && $hover_effect=='effect1' ){   
        $output ='<div class="kov-items circle effect1">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="spinner"></div>
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                          </div>
                        </div>
                    </a>
                </div>';
    }

    else if ( $hover_style=='circle' && $hover_effect=='effect2' ){
        $output ='<div class="kov-items circle effect2 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>
                    ';
    }   
     
    else if ( $hover_style=='circle' && $hover_effect=='effect3' ){
        $output ='<div class="kov-items circle effect3 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
      
     
    else if ( $hover_style=='circle' && $hover_effect=='effect4' ){
        $output ='<div class="kov-items circle effect4 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                       <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
         
    else if ( $hover_style=='circle' && $hover_effect=='effect5' ){
        $output ='<div class="kov-items circle effect5">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div class="info">
                        <div class="info-back">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </div>
                        </a>
                    </div>';
    }
    
         
    else if ( $hover_style=='circle' && $hover_effect=='effect6' ){
        $output ='<div class="kov-items circle effect6 scale_up">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    
         
    else if ( $hover_style=='circle' && $hover_effect=='effect7' ){
        $output ='<div class="kov-items circle effect7 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                         <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    
        else if ( $hover_style=='circle' && $hover_effect=='effect8' ){
        $output ='<div class="kov-items circle effect8 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                     <div class="img-container">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        </div>
                        <div class="info-container">
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </div>
                        </a>
                    </div>';
    }
        else if ( $hover_style=='circle' && $hover_effect=='effect9' ){
        $output ='<div class="kov-items circle effect9 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                       <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
            else if ( $hover_style=='circle' && $hover_effect=='effect10' ){
        $output ='<div class="kov-items circle effect10 top_to_bottom">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    
            else if ( $hover_style=='circle' && $hover_effect=='effect11' ){
        $output ='<div class="kov-items circle effect11 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    
    else if ( $hover_style=='circle' && $hover_effect=='effect12' ){
        $output ='<div class="kov-items circle effect12 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
        
            else if ( $hover_style=='circle' && $hover_effect=='effect13' ){
        $output ='<div class="kov-items circle effect13 from_left_and_right">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div class="info">
                        <div class="info-back">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </div>
                        </a>
                    </div>';
    }
    
      else if ( $hover_style=='circle' && $hover_effect=='effect14' ){
        $output ='<div class="kov-items circle effect14 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                       <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
     else if ( $hover_style=='circle' && $hover_effect=='effect15' ){
        $output ='<div class="kov-items circle effect15 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }  
    
    else if ( $hover_style=='circle' && $hover_effect=='effect16' ){
        $output ='<div class="kov-items circle effect16 left_to_right">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                       <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
        else if ( $hover_style=='circle' && $hover_effect=='effect17' ){
        $output ='<div class="kov-items circle effect17">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    
            else if ( $hover_style=='circle' && $hover_effect=='effect18' ){
        $output ='<div class="kov-items circle effect18 '.$hover_animation.'">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div style="background:'.$bg_color.';" class="info">
                        <div class="info-back">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </div>
                        </a>
                    </div>';
    }
            else if ( $hover_style=='circle' && $hover_effect=='effect19' ){
        $output ='<div class="kov-items circle effect19">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                        <div class="info">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </a>
                    </div>';
    }
    else if ( $hover_style=='circle' && $hover_effect=='effect20' ){
        $output ='<div class="kov-items circle effect20 top_to_bottom">
                    <a target="'.$link_new_tab.'" href="'.$hover_link.'">
                        <div class="img"><img src="'.$hover_image[0].'"></div>
                      <div class="info">
                        <div class="info-back">
                           <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
                            <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
                        </div>
                        </div>
                        </a>
                    </div>';
    }
       
    if ( $hover_style=='square' && $hover_effect=='effect1' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect1 left_and_right"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
           
    else if ( $hover_style=='square' && $hover_effect=='effect2' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect2"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect3' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect3 bottom_to_top"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect4' ){   
        $output ='
    <div style="border:'.$remove_border.';"  class="kov-items square colored effect4"><a href="#">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
        <div class="mask1"></div>
        <div class="mask2"></div>
         <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 

        else if ( $hover_style=='square' && $hover_effect=='effect5' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect5 left_to_right"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 

        else if ( $hover_style=='square' && $hover_effect=='effect6' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect6 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect7' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square colored effect7"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect8' ){   
        $output ='
        <div style="border:'.$remove_border.';" class="kov-items square effect8 scale_up"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect9' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect9 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
          <div class="info-back">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
          </div>
        </div></a></div>
        ';
    } 

        else if ( $hover_style=='square' && $hover_effect=='effect10' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect10 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect11' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect11 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect12' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect12 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect13' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect13 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect14' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect14 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style=='square' && $hover_effect=='effect15' ){   
        $output ='
   <div style="border:'.$remove_border.';" class="kov-items square effect15 '.$hover_animation.'"><a target="'.$link_new_tab.'" href="'.$hover_link.'">
        <div class="img"><img src="'.$hover_image[0].'" alt="img"></div>
       <div style="background:'.$bg_color.';" class="info">
        <h3 style=" font-size: '.$heading_font_size.'; color:'.$heading_color.'; ">'.$hover_heading.'</h3>
         <p style=" font-size: '.$desc_font_size.'; color:'.$description_color.'; border: '.$remove_hor_line.' ">'.$hov_desc.'</p>
        </div></a></div>
        ';
    } 
        else if ( $hover_style =='square' && $hover_effect=='effect16' || $hover_style =='square' && $hover_effect=='effect17'  ||   $hover_style =='square' && $hover_effect=='effect18' ||  $hover_style =='square' && $hover_effect=='effect20'){   
        $output ='
                    <h2>This Effects isn\'t available right now. we Are trying to provide more wonderful hover effects.</h2>
        ';
    } 
    return $output;
}
add_shortcode('hover_effects', 'hover_image_shortcode');    
?>