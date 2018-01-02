<?php

class Mango_Settings_Section {

	private $args;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function __construct( $args ) {
		$defaults = array(
			'id'			=> NULL,
			'title'			=> NULL,
			'page'			=> NULL,
			'description'	=> NULL,
		);
		$args = wp_parse_args( $args, $defaults );
		$this->args = $args;
		$this->register_section();
	}

	/**
	 * register_section function.
	 *
	 * @access private
	 * @param mixed $args
	 * @return void
	 */
	private function register_section() {
		add_settings_section(
			$this->args['id'],
			$this->args['title'],
			array($this, 'output_callback'),
			$this->args['page']
		);
	}

	/**
	 * output_callback function.
	 *
	 * @access public
	 * @return void
	 */
	public function output_callback() {
		?>
			<p><?php echo $this->args['description'] ?></p>
		<?php
	}

}
