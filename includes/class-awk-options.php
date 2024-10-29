<?php

/**
 * Master option class
 * 
 * @package Bolts
 * @since 1.0
 * @credits http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 * http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */
class AWK_Options {
	
	private $sections;
	private $checkboxes;
	private $settings;
	
	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		
		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		
		$this->sections['general']      = __( 'General Settings' );
		$awk_options = get_option( 'awk_options' );
		//font embedding setting tab
		$embeded_fonts= ($awk_options['fonts_embeded'] ? 'yes':'');
		if ($embeded_fonts == 'yes'){
		$this->sections['fonts_embed']   = __( 'Fonts Embed' );
		$this->sections['fonts_server']   = __( 'Fonts Server' );
		}
		//ayar toolbar setting tab
		$ayar_toolbar= ($awk_options['ayar_toolbar'] ? 'yes':'');
		if ($ayar_toolbar == 'yes'){
		$this->sections['toolbar_sect']        = __( 'Ayar Toolbar' );
		}
		//converter setting tab
		$converter_sect= ($awk_options['converter'] ? 'yes':'');
		if ($converter_sect == 'yes'){
		$this->sections['converter_sect']        = __( 'Converter' );
		}
		//template setting tab
		$template_sect= ($awk_options['template'] ? 'yes':'');
		if ($template_sect == 'yes'){
		$this->sections['template_sect']        = __( 'Template' );
		}
		//localization setting tab
		$locale_sect= ($awk_options['localization'] ? 'yes':'');
		if ($locale_sect == 'yes'){
		$this->sections['locale_sect']        = __( 'Localization' );
		}
		$this->sections['reset']        = __( 'Reset to Defaults' );
		$this->sections['about']        = __( 'About' );
		
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'awk_options' ) )
			$this->initialize_settings();
		
	}
	
	/**
	 * Add options page
	 *
	 * @since 1.0
	 */
	public function add_pages() {
			$page_title = "Ayar Web Kit";
			$menu_title = "AyarWebKit";
			$capability = "manage_options";
			$menu_slug = "ayarwebkit";
			$function = array( &$this, 'display_page' );
			$icon_url = AWK_PLUGIN_URL."/icons/webfont.png";
			$position = null;
			$admin_page = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );		
		//$admin_page = add_theme_page( __( 'Ayar Web Kit' ), __( 'Ayar Web Kit' ), 'manage_options', 'ayarwebkit', array( &$this, 'display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
		
	}
	
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( 'Default Field' ),
			'desc'    => __( 'This is a default description.' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'ayarwebkit', $section, $field_args );
	}
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		
		echo '<div class="wrap">';
		screen_icon('awk-settings'); 
		echo '<h2>' . __( 'Ayar Web Kit' ) . '</h2>';
	
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
		
		echo '<form action="options.php" method="post">';
	
		settings_fields( 'awk_options' );
		echo '<div class="ui-tabs">
			<ul class="ui-tabs-nav">';
		
		foreach ( $this->sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		
		echo '</ul>';
		do_settings_sections( $_GET['page'] );
		
		echo '</div>
		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>
		
	</form>';
	
	echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];';
			
			foreach ( $this->sections as $section_slug => $section )
				echo "sections['$section'] = '$section_slug';";
			
			echo 'var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
			});
			$(".ui-tabs-panel").each(function(index) {
				$(this).attr("id", sections[$(this).children("h3").text()]);
				if (index > 0)
					$(this).addClass("ui-tabs-hide");
			});
			$(".ui-tabs").tabs({
				fx: { opacity: "toggle", duration: "fast" }
			});
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			$(".wrap h3, .wrap table").show();
			
			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
			
			// Browser compatibility
			if ($.browser.mozilla) 
			         $("form").attr("autocomplete", "off");
		});
	</script>
</div>';
		
	}
	
	/**
	 * Description for section
	 *
	 * @since 1.0
	 */
	public function display_section() {
		// code
	}
	
	/**
	 * Description for About section
	 *
	 * @since 1.0
	 */
	public function display_about_section() {
		
		// This displays on the "About" tab. Echo regular HTML here, like so:
		// echo '<p>Copyright 2011 me@example.com</p>';
		
	}
	
	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {
		
		extract( $args );
		
		$awk_options = get_option( 'awk_options' );
		
		if ( ! isset( $awk_options[$id] ) && $type != 'checkbox' )
			$awk_options[$id] = $std;
		elseif ( ! isset( $awk_options[$id] ) )
			$awk_options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;
			
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="awk_options[' . $id . ']" value="1" ' . checked( $awk_options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				
				break;
			
			case 'select':
				echo '<select class="select' . $field_class . '" name="awk_options[' . $id . ']">';
				
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $awk_options[$id], $value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="awk_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $awk_options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $awk_options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="awk_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $awk_options[$id] ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="awk_options[' . $id . ']" value="' . esc_attr( $awk_options[$id] ) . '" />';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="awk_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $awk_options[$id] ) . '" />';
		 		
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		
		 		break;
		 	
		}
		
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
	public function get_settings() {
		$awk_options = get_option( 'awk_options' );		
		/* General Settings
		===========================================*/
		
		$this->settings['fonts_embeded'] = array( //font embedding
			'section' => 'general',
			'title'   => __( 'Enable Fonts Emebedding Settings' ),
			'desc'    => __( 'check this if you want to enable font embedding settings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['ayar_toolbar'] = array( //ayar toolbar
			'section' => 'general',
			'title'   => __( 'Enable Ayar Toolbar and Settings' ),
			'desc'    => __( 'check this if you want to enable ayar toolbar and settings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['converter'] = array( //ayar converter
			'section' => 'general',
			'title'   => __( 'Enable Encoding converter and Settings' ),
			'desc'    => __( 'check this if you want to enable encoding converter and settings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['template'] = array( //ayar template
			'section' => 'general',
			'title'   => __( 'Enable Template Functions' ),
			'desc'    => __( 'check this if you want to add new functionality to your theme.<br />(You can set font-family for common html tags, your custom ID and Class and many more features.)' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['localization'] = array( //Localization
			'section' => 'general',
			'title'   => __( 'Enable Localization Settings' ),
			'desc'    => __( 'check this if you want to enable localization settings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		/*Font Embed*/
		$embeded_fonts= ($awk_options['fonts_embeded'] ? 'yes':'');
		if ($embeded_fonts == 'yes'){
		/* Font Embed
		===========================================*/
		
		$this->settings['ayar'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar' ),
			'desc'    => __( 'Check to embed Ayar font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_takhu'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Takhu' ),
			'desc'    => __( 'Check to embed Ayar Takhu font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_kasone'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Kasone' ),
			'desc'    => __( 'Check to embed Ayar Kasone font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_nayon'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Nayon' ),
			'desc'    => __( 'Check to embed Ayar Nayon font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_wazo'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Wazo' ),
			'desc'    => __( 'Check to embed Ayar Wazo font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_wagaung'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Wagaung' ),
			'desc'    => __( 'Check to embed Ayar Wagaung font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_tawthalin'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Tawthalin' ),
			'desc'    => __( 'Check to embed Ayar Tawthalin font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_thadingyut'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Thadingyut' ),
			'desc'    => __( 'Check to embed Ayar Thadingyut font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_tazaungmone'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Tazaungmone' ),
			'desc'    => __( 'Check to embed Ayar Tazaungmone font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_natdaw'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Natdaw' ),
			'desc'    => __( 'Check to embed Ayar Natdaw font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_pyatho'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Pyatho' ),
			'desc'    => __( 'Check to embed Ayar Pyatho font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_tapotwe'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Tapotwe' ),
			'desc'    => __( 'Check to embed Ayar Tapotwe font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_tabaung'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Tabaung' ),
			'desc'    => __( 'Check to embed Ayar Tabang font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_juno'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Juno' ),
			'desc'    => __( 'Check to embed Ayar Juno font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_typewriter'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Typewriter' ),
			'desc'    => __( 'Check to embed Ayar Typewriter font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['ayar_thawka'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Ayar Thawka' ),
			'desc'    => __( 'Check to embed Ayar Thawka font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['myanmar3'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Myanmar 3' ),
			'desc'    => __( 'Check to embed Myanmar 3 font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['zawgyi'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Zawgyi One' ),
			'desc'    => __( 'Check to embed Zawgyi One font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['padauk'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Padauk' ),
			'desc'    => __( 'Check to embed Padauk font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['yungkyio'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'YungKyio' ),
			'desc'    => __( 'Check to embed YungKyio font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['masterpiece'] = array(
			'section' => 'fonts_embed',
			'title'   => __( 'Masterpiece Uni Sans' ),
			'desc'    => __( 'Check to embed Masterpiece Uni Sans font on your site' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
			}
		/* Font Server 
		 ===========================================*/
		$this->settings['fonts_server_url'] = array(
			'section' => 'fonts_server',
			'title'   => __( 'Choose font server to use in embedding.' ),
			'desc'    => __( 'To choose fonts server for font embedding.' ),
			'type'    => 'radio',
			'std'     => 'default',
			'choices' => array(
				'default' => 'Default',
				'same_domain' => 'Same Domain (current wordpress installation server. You need to upload fonts in /wp-content/plugins/ayarwebkit/fonts/ folder.)',
				'other' => 'Your Own Fonts Server (You need to setup your own fonts server using package from http://webfont.myanmapress.com/ and enter your server url below.)'
			)
		);	
			$this->settings['other_server_url'] = array(
			'title'   => __( 'Fonts Server URL' ),
			'desc'    => __( 'Enter Fonts Server URL (Don\'t need to set this options if you do not select "Your Own Fonts Server")' ),
			'std'     => 'http://webfont.myanmapress.com/',
			'type'    => 'text',
			'section' => 'fonts_server'
		);

		/* Ayar Toolbar
		===========================================*/
		$this->settings['toolbar_pos'] = array(
			'section' => 'toolbar_sect',
			'title'   => __( 'Choose Toolbar Position' ),
			'desc'    => __( 'You can choose toolbar position' ),
			'type'    => 'radio',
			'std'     => 'bottomright',
			'choices' => array(
				'middledock' => 'Middle Dock',
				'topleft' => 'Top Left',
				'topright' => 'Top Right',
				'bottomleft' => 'Bottom Left',
				'bottomright' => 'Bottom Right'
			)
		);
		
		/* Converter
		===========================================*/
		$this->settings['converter_options'] = array(
			'section' => 'converter_sect',
			'title'   => __( 'Choose Convertion Method' ),
			'desc'    => __( 'You can choose which to which convert.' ),
			'type'    => 'radio',
			'std'     => 'ayar2zg',
			'choices' => array(
				'ayar2zg' => 'Ayar to Zawgyi (You need to write your content in Ayar)',
				'zg2ayar' => 'Zawgyi to Ayar (You need to write your content in Zawgyi)',
				'zg2uni' => 'Zawgyi to Unicode (You need to write your content in Zawgyi)'
			)
		);
		$converter=($awk_options['converter'] ? 'yes':'');
		if ($converter == 'yes'){
		$this->settings['mobile_enable'] = array( //mobile suppoet
			'section' => 'converter_sect',
			'title'   => __( 'Enable Mobile Device support' ),
			'desc'    => __( 'check this if you want to enable mobile device settings.<br />This can convert your unicode content to zawgyi encoding' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['rss_enable'] = array( //mobile suppoet
			'section' => 'converter_sect',
			'title'   => __( 'Enable RSS encoding convertion support' ),
			'desc'    => __( 'check this if you want to enable RSS encoding convertion support.<br />This can convert your unicode content to zawgyi encoding' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		}
		
		/* Template Functionality
		===========================================*/
		$this->settings['font_family_head'] = array(
			'section' => 'template_sect',
			'title'   => '', // Not used for headings.
			'desc'    => 'Font Family Settings',
			'type'    => 'heading'
		);
		$this->settings['h1_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H1' ),
			'desc'    => __( 'Select font for H1 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h2_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H2' ),
			'desc'    => __( 'Select font for H2 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h3_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H3' ),
			'desc'    => __( 'Select font for H3 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h4_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H4' ),
			'desc'    => __( 'Select font for H4 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h5_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H5' ),
			'desc'    => __( 'Select font for H5 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h6_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H6' ),
			'desc'    => __( 'Select font for H6 tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h1a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H1 A' ),
			'desc'    => __( 'Select font for H1 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h2a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H2 A' ),
			'desc'    => __( 'Select font for H2 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h3a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H3 A' ),
			'desc'    => __( 'Select font for H3 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h4a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H4 A' ),
			'desc'    => __( 'Select font for H4 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h5a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H5 A' ),
			'desc'    => __( 'Select font for H5 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['h6a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'H6 A' ),
			'desc'    => __( 'Select font for H6 A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['p_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'P' ),
			'desc'    => __( 'Select font for P tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['pa_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'P A' ),
			'desc'    => __( 'Select font for P A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['a_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'A' ),
			'desc'    => __( 'Select font for A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['div_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'DIV' ),
			'desc'    => __( 'Select font for DIV tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['li_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'LI' ),
			'desc'    => __( 'Select font for LI tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['lia_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'LI A' ),
			'desc'    => __( 'Select font for LI A tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['span_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'SPAN' ),
			'desc'    => __( 'Select font for SPAN tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['input_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'INPUT' ),
			'desc'    => __( 'Select font for INPUT tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);
		$this->settings['label_font'] = array(
			'section' => 'template_sect',
			'title'   => __( 'LABEL' ),
			'desc'    => __( 'Select font for LABEL tag.' ),
			'type'    => 'select',
			'std'     => 'none',
			'choices' => array(
				'none' => 'None',
				'Ayar' => 'Ayar',
				'Ayar Takhu' => 'Ayar Takhu',
				'Ayar Kasone' => 'Ayar Kasone',
				'Ayar Nayon' => 'Ayar Nayon',
				'Ayar Wazo' => 'Ayar Wazo',
				'Ayar Wagaung' => 'Ayar Wagaung',
				'Ayar Tawthalin' => 'Ayar TawThaLin',
				'Ayar Thadingyut' => 'Ayar Thadingyut',
				'Ayar Tazaungmone' => 'Ayar Tazaungmone',
				'Ayar Natdaw' => 'Ayar Natdaw',
				'Ayar Pyatho' => 'Ayar Pyatho',
				'Ayar Tapotwe' => 'Ayar Tapotwe',
				'Ayar Tabaung' => 'Ayar Tabaung',
				'Ayar Juno' => 'Ayar Juno',
				'Ayar Typewriter' => 'Ayar Typewriter',
				'Ayar Thawka' => 'Ayar Thawka',
				'Myanmar3' => 'Myanmar3',
				'Padauk' => 'Padauk',
				'Parabaik' => 'Parabaik',
				'Masterpiece Uni Sans' => 'Masterpiece Uni Sans',
				'Yungkyio' => 'Yungkyio',
				'Zawgyi-One' => 'Zawgyi-One'
			)
		);

		$this->settings['calendar_head'] = array(
			'section' => 'template_sect',
			'title'   => '', // Not used for headings.
			'desc'    => 'Calendar Widget Header Settings',
			'type'    => 'heading'
		);
		$this->settings['my_calendar_head'] = array( //calendar suppoet
			'section' => 'template_sect',
			'title'   => __( 'Enable image icon in Burmese Calendar Widget support' ),
			'desc'    => __( 'check this if you want to enable image icons in Calendar Widget' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['my_cal_img_width'] = array( //calendar suppoet
			'title'   => __( 'Image Width for calendar widget header' ),
			'desc'    => __( 'You can set this in pixel or in percent. E.g : 15px or 80% <br />This should be in between 15px and 30px.' ),
			'std'     => 'auto',
			'type'    => 'text',
			'section' => 'template_sect'
		);
		$this->settings['my_calendar_widget'] = array( //calendar suppoet
			'section' => 'template_sect',
			'title'   => __( 'Enable Burmese Lunar Calendar Widget support' ),
			'desc'    => __( 'check this if you want to enable traditional Burmese Lunar Calendar Widget' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['meta_tag_head'] = array(
			'section' => 'template_sect',
			'title'   => '', // Not used for headings.
			'desc'    => 'Html Meta Tag',
			'type'    => 'heading'
		);
		$this->settings['meta_tag'] = array( //calendar suppoet
			'section' => 'template_sect',
			'title'   => __( 'Enable http-equiv and content type meta tag' ),
			'desc'    => __( 'check this if your unicode content display in utf8 encoded and htmlentities (i.e : in planet ape\'s language)<br />This will correct unicode in utf8 encode and htmlentities' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		$locale_sect=($awk_options['localization'] ? 'yes':'');
		if ($locale_sect == 'yes'){
		$this->settings['my_calendar'] = array( //calendar suppoet
			'section' => 'locale_sect',
			'title'   => __( 'Enable Burmese Calendar Widget support' ),
			'desc'    => __( 'check this if you want to enable Calendar Widget in Burmese/Myanmar Language' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		}
		/* Reset
		===========================================*/
		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset theme' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
		
	}
	
	/**
	 * Initialize settings to their default values
	 * 
	 * @since 1.0
	 */
	public function initialize_settings() {
		
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
		
		update_option( 'awk_options', $default_settings );
		
	}
	
	/**
	* Register settings
	*
	* @since 1.0
	*/
	public function register_settings() {
		
		register_setting( 'awk_options', 'awk_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( &$this, 'display_about_section' ), 'ayarwebkit' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'ayarwebkit' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
		
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function scripts() {
		
		wp_print_scripts( 'jquery-ui-tabs' );
		//wp_enqueue_script( 'thickbox' );
		
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function styles() {
		
		wp_register_style( 'awk-admin', AWK_PLUGIN_URL . '/styles/awk-options.css' );
		wp_enqueue_style( 'awk-admin' );
		//wp_enqueue_style( 'thickbox' );
		$icon_url = AWK_PLUGIN_URL."/icons/webfont-35.png";
		echo <<<AWK
		<style type="text/css">
		#icon-awk-settings{
			background: transparent url('$icon_url') no-repeat;
			}
		</style>
AWK;
		
	}
	
	/**
	* Validate settings
	*
	* @since 1.0
	*/
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$awk_options = get_option( 'awk_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $awk_options[$id] ) && ! isset( $input[$id] ) )
					unset( $awk_options[$id] );
			}
			
			return $input;
		}
		return false;
		
	}
	
}

$awk_options = new AWK_Options();

function awk_option( $awk_option ) {
	$awk_options = get_option( 'awk_options' );
	if ( isset( $awk_options[$awk_option] ) )
		return $awk_options[$awk_option];
	else
		return false;
}
?>
