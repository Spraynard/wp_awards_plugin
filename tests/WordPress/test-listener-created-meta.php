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
class Test_Listener_Created_Meta extends WP_UnitTestCase {
	// Post type to test against.
	private $post;
	private $user;
	private $wpdb;
	private $plugin_basename;

	// Set up award posts before we run any tests.
	public function setUp() {
		parent::setUp();

		// Get an instance of the wordpress db
		global $wpdb;
		$this->wpdb = $wpdb;

		if ( ! defined('USER_AWARDS_POST_TYPE') )
		{
			$this->fail("USER_AWARDS_POST_TYPE CONSTANT IS NOT AVAILABLE");
		}

		$this->post = $this->factory->post->create_and_get(array(
			'post_type' => USER_AWARDS_POST_TYPE,
			'post_title' => 'Eighty Hours Worked',
			'post_content' => 'Awarded to users if they have more than 80 hours worked for us. They are really nice people',
			'post_author' => 1,
			'meta_input' => array(
				USER_AWARDS_GRAMMAR_META_TYPE => "CURRENT_USER_META CREATED WHERE total_hours GTEQ 80"
			)
		));

		$this->post_2 = $this->factory->post->create_and_get(array(
			'post_type' => USER_AWARDS_POST_TYPE,
			'post_title' => 'Zero Fish Available',
			'post_content' => 'Awarded to users if they have no fish in their bucket',
			'post_author' => 1,
			'meta_input' => array(
				USER_AWARDS_GRAMMAR_META_TYPE => "CURRENT_USER_META CREATED WHERE fish_in_bucket eq 0"
			)
		));

		$this->post_3 = $this->factory->post->create_and_get(array(
			'post_type' => USER_AWARDS_POST_TYPE,
			'post_title' => 'Zero Fish Available',
			'post_content' => 'Awarded to users if they have no fish in their bucket',
			'post_author' => 1,
			'meta_input' => array(
				USER_AWARDS_GRAMMAR_META_TYPE => "CURRENT_USER_META CREATED WHERE fish_in_bucket eq -1"
			)
		));

		// Create a user
		$this->user = $this->factory->user->create_and_get();

		// Set the named user as the current user
		wp_set_current_user( $this->user->get('ID') );
	}

	// Creating meta on a user that satisfies award requirements should award people.
	public function testSuccessfulAwardOnCreate() {
		$post = $this->post;
		$user = wp_get_current_user();

		// Update/Create before we trip the successful award update.
		$UserAwards = new UserAwards\BusinessLogic\Core( $this->wpdb );
		$Grammar = new UserAwards\Grammar\Core();

		$UserAwards_Grammar = get_post_meta( $post->ID, USER_AWARDS_GRAMMAR_META_TYPE, true);
		$Grammar->parse($UserAwards_Grammar);

		// Fail test if we do not listen correctly
		try {
			$listener = new UserAwards\Listener\Core( $post->ID, $Grammar, $UserAwards );
			$listener->add_listeners( $user );
		} catch ( Exception $e ) {
			$this->fail("Test Failure Occured: " . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() );
		}

		// Listeners should be available now. Add meta to our users.
		$user_meta_updated = add_user_meta( $user->ID, $Grammar->trigger->descriptor, 80 );

		if ( ! $user_meta_updated ) {
			$this->fail("User Meta was not updated correctly");
		}

		// Check to see if our listener assigned an award to this user
		$award_data = $UserAwards->GetUserAward( $user->ID );

		$this->assertNotEmpty($award_data, "Should have an award assigned to our user, but our data does not show as such.");
	}

	// Should be able to award a user on update of user meta.
	// An award requirement of when a user needs to have *zero* of something should still pass this test.
	public function testSuccessfulAwardWhenUserMetaIsZero() {
		$post = $this->post_2;
		$user = wp_get_current_user();


		$UserAwards = new UserAwards\BusinessLogic\Core( $this->wpdb );
		$Grammar = new UserAwards\Grammar\Core();

		$UserAwards_Grammar = get_post_meta( $post->ID, USER_AWARDS_GRAMMAR_META_TYPE, true);
		$Grammar->parse($UserAwards_Grammar);

		// Fail test if we do not listen correctly
		try {
			$listener = new UserAwards\Listener\Core( $post->ID, $Grammar, $UserAwards );
			$listener->add_listeners( $user );
		} catch ( Exception $e ) {
			$this->fail("Test Failure Occured: " . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() );
		}

		// Update our user meta to a certain point
		$user_meta_added = add_user_meta( $user->ID, $Grammar->trigger->descriptor, 0, false );

		if ( ! $user_meta_added ) {
			$this->fail("User Meta was not created correctly");
		}

		// Check to see if our listener assigned an award to this user
		$award_data = $UserAwards->GetUserAward( $user->ID );

		$this->assertNotEmpty($award_data, "Should have an award assigned to our user, but our data does not show as such.");
	}

	// Should be able to award a user on update of user meta.
	// An award requirement of when a user needs to have *zero* of something should still pass this test.
	public function testSuccessfulAwardWhenUserMetaIsNegative() {
		$post = $this->post_3;
		$user = wp_get_current_user();


		$UserAwards = new UserAwards\BusinessLogic\Core( $this->wpdb );
		$Grammar = new UserAwards\Grammar\Core();

		$UserAwards_Grammar = get_post_meta( $post->ID, USER_AWARDS_GRAMMAR_META_TYPE, true);
		$Grammar->parse($UserAwards_Grammar);

		// Fail test if we do not listen correctly
		try {
			$listener = new UserAwards\Listener\Core( $post->ID, $Grammar, $UserAwards );
			$listener->add_listeners( $user );
		} catch ( Exception $e ) {
			$this->fail("Test Failure Occured: " . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() );
		}

		// Update our user meta to a certain point
		$user_meta_added = add_user_meta( $user->ID, $Grammar->trigger->descriptor, -1, false );

		if ( ! $user_meta_added ) {
			$this->fail("User Meta was not created correctly");
		}

		// Check to see if our listener assigned an award to this user
		$award_data = $UserAwards->GetUserAward( $user->ID );

		$this->assertNotEmpty($award_data, "Should have an award assigned to our user, but our data does not show as such.");
	}
}
?>