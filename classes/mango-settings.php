<?php

final class Mango_Settings{

  function __construct() {
    add_action( 'admin_menu', array( &$this, 'add_settings_page' ) );
    add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
    add_action( 'admin_enqueue_scripts',array( &$this, 'admin_scripts' ) );
	}

  public function admin_scripts() {
    wp_register_style( 'mango_admin_style', MANGO__PLUGIN_URL . 'admin/admin.css', false, Mango::$version );
    wp_register_script( 'mango_admin_script', MANGO__PLUGIN_URL . 'admin/admin.js', array( 'jquery' ), Mango::$version, true );
    
    wp_enqueue_style( 'mango_admin_style' );
		wp_enqueue_script( 'mango_admin_script' );
  }

  public function register_settings() {
		// general
		$args = array(
      'id'			    => 'mango_settings',
			'title'			  => __( 'General', Mango::$slug ),
			'page'			  => 'mango_settings_page',
			'description'	=> __( 'These settings control the general setup of the Mango plugin.', Mango::$slug ),
    );
    $settings = new Mango_Settings_Section( $args );

		$args = array(
			'id'				    => 'mango_enabled',
			'title'				  => __( 'Enabled' ),
			'page'				  => 'mango_settings_page',
			'section'			  => 'mango_settings',
			'description'	  => __( '' ),
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> 'settings_page_mango_settings_page',
		);
		$enabled = new Mango_Settings_Field( $args );

		// resources
    $args = array(
      'id'			    => 'mango_resources',
			'title'			  => __( 'Resources', Mango::$slug ),
			'page'			  => 'mango_settings_page',
			'description'	=> __( 'These are all the additional resources Mango provides to Wordpress.', Mango::$slug ),
    );
    $resources = new Mango_Settings_Section( $args );

    $args = array(
			'id'				    => 'mango_nav',
			'title'				  => __( 'Navigation Menus' ),
			'page'				  => 'mango_settings_page',
			'section'			  => 'mango_resources',
			'description'	  => __( '' ),
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> 'settings_page_mango_settings_page',
		);
		$nav = new Mango_Settings_Field( $args );

  }

  public function add_settings_page() {
    $settings_page = add_options_page(
      __( 'mango', mango::$slug ),
      __( 'mango', mango::$slug ),
      'manage_options',
      'mango_settings_page',
      array( &$this, 'settings_page' )
    );
  }

  public function settings_page() {
    ?>
		<div class="wrap mango-settings-page">
			<h2><?php _e( 'Mango', mango::$slug ); ?></h2>
			<form action="options.php" method="post">
			<?php
				global $wp_settings_sections, $wp_settings_fields;
				settings_fields('settings_page_mango_settings_page');
				$page = 'mango_settings_page';
			?>
			<div class="container-fluid settings-container">
				<div class="row container-row">
					<div class="col-xs-12 col-sm-4 col-md-3 navigation-container">
						<ul class="navigation">
						<?php
							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<li class="nav-item">';
										echo '<a href="#'.$section['id'].'">';
											if($section['icon'])
												echo '<i class="fa fa-'.$section['icon'].'"></i> ';
											echo '<span class="hidden-xs">' . $section['title'] . '</span>';
										echo '</a>';
									echo '</li>';
								}
							}
						?>
						</ul>
					</div>
					<div class="col-xs-12 col-sm-8 col-md-9 content-container">
						<?php
							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<div class="section" id="section-'.$section['id'].'">';
									if ( $section['icon'] ) {
										$icon = "<i class='fa fa-{$section['icon']}'></i>";
									} else {
										$icon = null;
									}
									if ( $section['title'] )
										echo "<h2>$icon {$section['title']}</h2>\n";
									if ( $section['callback'] )
										call_user_func( $section['callback'], $section );
									if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
										echo '</div>';
										continue;
									}
									echo '<table class="form-table">';
									do_settings_fields( $page, $section['id'] );
									echo '</table>';
									echo '
				          <p class="submit">
					          <input name="Submit" type="submit" class="button-primary" value="'.esc_attr('Save Changes','gb').'" />
				          </p>';
									echo '</div>';
								}
							}
						?>
					</div>
				</div>
			</div>
			</form>


			<div class="credits-container">
				<div class="row">
					<div class="col-xs-12 col-sm-6"><?= mango::$version ?></div>
				</div>
			</div>
		</div><!-- wrap -->
		<?php
  }

	public function admin_notices(){
		if ( isset($_GET['page']) && $_GET['page'] !== 'mango_settings_page' ){
			return;
		}

		if ( isset( $_GET['settings-updated']) && $_GET['settings-updated'] === true ){
			add_settings_error( 'mango_settings_page', 'mango_settings_page', __( 'Successfully updated.' ) , 'updated');
		}

		settings_errors( 'mango_settings_page' );
	}

  protected function __clone() {

  }
}
