<?php
//Loading CSS
function kingrd_anibox_css() {
    wp_enqueue_style('kingcom_animate_box_css', plugins_url( '/css/style.css' , __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'kingrd_anibox_css' );
add_action('init', 'kingrd_animate_box_params', 99 );
function kingrd_animate_box_params(){
    global $kc;
    $kc->add_map(
        array(
            'kingrd_animate' => array(
                'name' => 'Animate Box',
                'description' => __('Animation Box', 'KingComposer'),
                'icon' => 'kc-icon-flip',
                //'is_container' => true,
                'category' => 'KC RD Extensions',
                'css_box'    => true,
                'params' => array(
                    array(
                        'name' => 'image_icon',
                        'label' => 'Upload Box Image',
                        'type' => 'attach_image',
                        ),
                    array(
                        'type'			=> 'text',
                        'label'			=> __( 'Title', 'kingcomposer' ),
                        'name'			=> 'title',
                        'description'	=> __( 'Title of the progress bar. Leave blank if no title is needed.', 'kingcomposer' ),
                        'admin_label'	=> true,
                        ), 

                    array(
                        'type'			=> 'textarea',
                        'label'			=> __( 'Description', 'kingcomposer' ),
                        'name'			=> 'descript',
                        'description'	=> __( 'Description Goes Here.', 'kingcomposer' ),
                        'value'         => 'Description Goes Here',
                        'admin_label'	=> true,
                        ),
                      array(
                        'name' => 'bg_color',
                        'label' => 'Background Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                         'value' => '#252525'
                    ),
                       array(
                        'name' => 'title_f_size',
                        'label' => 'Title font size',
                        'type' => 'number_slider',
                        'options' => array(
                            'min' => 0,
                            'max' => 40,
                            'unit' => 'px',
                            'show_input' => true
                        ),
                        'value' => '18',
                        'description' => 'Chose Title Font Size here, Default is 18px'
                    ),

                       array(
                        'name' => 'descr_f_size',
                        'label' => 'Description font size',
                        'type' => 'number_slider',
                        'options' => array(
                            'min' => 0,
                            'max' => 40,
                            'unit' => 'px',
                            'show_input' => true
                        ),
                        'value' => '14',
                        'description' => 'Chose Description Font Size here, Default is 14px'
                    ), 
                     array(
                        'name' => 'title_color',
                        'label' => 'Title Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                    ),
                     array(
                        'name' => 'descr_color',
                        'label' => 'Description Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                    ),

                    )
                )
            )
        );
}
// Register Hover Shortcode
function kingrd_animatebox_shortcode($atts, $content = null){
    extract( shortcode_atts( array(
        'title' => '',
        'descript' => '',
        'image_icon' => '',
        'css' => '',
        'title_f_size' => '',
        'descr_f_size' => '',
        'title_color' => '',
        'descr_color' => '',
        'bg_color' => '',
        ), $atts) );
        echo '<style type="text/css">
            .homeBox .one_fourth:hover{
                background:'.$bg_color.';
            }</style>';
    //$descript = base64_decode( $atts['descript'] );
    $image_icon = wp_get_attachment_image_src( $image_icon,'full' );
    $output ='
    <div class="homeBox">
        <div class="one_fourth '.$css.'">
            <div class = "boxImage"><img style="border-radius:0;" src = "'.$image_icon[0].'"></div>	
            <h2 style="font-size:'.$title_f_size.'; color:'.$title_color.';">'.strtoupper($title).'</h2>	
            <div class = "boxDescription">
            <p style="font-size:'.$descr_f_size.';  color:'.$descr_color.';">'.$descript.'</p>
            </div>	
        </div> 
    </div>
    ';
    return $output;
}
add_shortcode('kingrd_animate', 'kingrd_animatebox_shortcode');
