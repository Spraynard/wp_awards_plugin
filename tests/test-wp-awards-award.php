<?php
/**
 * Class WPAwardsTest
 *
 * @package  Wp_awards
 */

/**
 * Test the base functionality of our plugin.
 * We should be making sure all of the basic
 * functionality needed for this plugin
 * is supported.
 */
class Test_WP_Awards extends WP_UnitTestCase {
	// Post type to test against.
	private $post;
	private $user;
	private $wpdb;
	private $wp_award;
	private $plugin_basename;

	// Set up award posts before we run any tests.
	public function setUp() {
		parent::setUp();

		// Get an instance of the wordpress db
		global $wpdb;
		$this->wpdb = $wpdb;

		// // Activate our plugin
		// $this->plugin_basename = dirname( __DIR__ ) . '/wp_awards.php';
		// echo "Plugin Basename: " . $this->plugin_basename . "\n";
		// $plugin_activated = activate_plugin($this->plugin_basename);

		// if ( is_wp_error( $plugin_activated ) )
		// {
		// 	$this->fail($plugin_activated->get_error_message());
		// }

		// Assigning to our post variable.
		$this->post = $this->factory->post->create_and_get(array(
			'post_type' => 'wap_award',
			'post_title' => 'Fifty Hours Worked',
			'post_content' => 'Awarded to users if they have more than 50 hours worked for us. They are really nice people',
			'post_author' => 1,
			'meta_input' => array(
				'wap_grammar' => "CURRENT_USER_META UPDATED WHERE key=total_hours GTEQ 50"
			)
		));

		// Create a user
		$this->user = $this->factory->user->create_and_get();

		// Set the named user as the current user
		wp_set_current_user( $this->user->get('ID') );

		$this->wp_award = new WPAward\WPAward( $this->wpdb );
	}

	public function testGetUserAwardNoAssignments() {
		$user
	}

	public function testGetAwardSuccess() {
		$this->assertTrue(true);
	}

	public function testAssignAward() {
		$this->assertTrue(true);
	}

	public function testGiveAward() {
		$this->assertTrue(true);
	}

	public function testAutoGiveAwardOnAssign() {
		$this->assertTrue(true);
	}

	public function testRemoveAward() {
		$this->assertTrue(true);
	}
}

?>