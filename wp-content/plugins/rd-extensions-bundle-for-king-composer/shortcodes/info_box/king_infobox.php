<?php
//Loading CSS
function kingrd_infobox_css() {
    wp_enqueue_style('kingcom_infobox_box_css', plugins_url( '/css/style.css' , __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'kingrd_infobox_css' );
add_action('init', 'kingrd_infobox_box_params', 99 );
function kingrd_infobox_box_params(){
    global $kc;
    $kc->add_map(
        array(
            'kingrd_infobox' => array(
                'name' => 'infobox Box',
                'description' => __('Animation Box', 'KingComposer'),
                'icon' => 'kc-icon-box',
                //'is_container' => true,
                'category' => 'KC RD Extensions',
                'css_box'    => true,
                'params' => array(
                 'General' => array(
                    array(
                        'name' => 'icon_p',
                        'label' => 'Select Icon',
                        'type' => 'icon_picker',
                        'admin_label' => true,
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
                    'label'			=> __( 'Title', 'kingcomposer' ),
                    'name'			=> 'descr',
                    'description'	=> __( 'Title of the progress bar. Leave blank if no title is needed.', 'kingcomposer' ),
                    'admin_label'	=> true,
                ),
                ),
                
              'Typography' => array(
                     array(
                        'name' => 'icon_color',
                        'label' => 'icon Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                        'value' => '#FAFAFA',
                    ), 
                    array(
                        'name' => 'icon_bg_color',
                        'label' => 'icon Background Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                        'value' => '#4A4A4A',

                    ),
                    array(
                        'name' => 'title_color',
                        'label' => 'Title Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                        'value' => '#4A4A4A',

                    ), 
                      array(
                        'name' => 'descr_color',
                        'label' => 'Description Color',
                        'type' => 'color_picker',
                        'admin_label' => true,
                        'value' => '#4A4A4A',
                        
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
                        'value' => '20',
                        'description' => 'Chose Title Font Size here, Default is 14px'
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
                ),
                      
                )
            )
        )
    );
}
// Register Hover Shortcode
function kingrd_infoboxbox_shortcode($atts, $content = null){
    extract( shortcode_atts( array(
        'title' => 'Title Goes here',
        'descr' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet',
        'icon_p' => '',
        'css' => '',
        'icon_color' => '',
        'title_color' => '',
        'descr_color' => '',
        'title_f_size' => '',
        'descr_f_size' => '',
        'icon_bg_color' => '#38332b',
    ), $atts) );
     $image_icon = wp_get_attachment_image_src( $image_icon,'full' );
        $output ='
<div class="infobox">
					<div class="four nospace-left">
					    <div id="infobox-1" class="infobox-1">
							<div class="infobox-holder '.$css.'">
                                <div class="dt-zoom-animation">							
									<i style="background-color:'.$icon_bg_color.';color:'.$icon_color.';" class="fa '.$icon_p.'"></i>
									<h4 style="color:'.$title_color.';  font-size:'.$title_f_size.';">'.$title.'</h4>
									<p style="color:'.$descr_color.'; font-size:'.$descr_f_size.';">'.$descr.'</p>
								</div>
							</div>
						</div>
					</div>
				</div>
        ';
             $output .= '<style type="text/css">         
   
    .infobox .infobox-holder .dt-zoom-animation i:after {
        box-shadow:0 0 0 4px '.$icon_bg_color.';
    }
     </style>';
    return $output;
}
add_shortcode('kingrd_infobox', 'kingrd_infoboxbox_shortcode');
