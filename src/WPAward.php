<?php
namespace WPAward;

/**
 * API for our plugin
 * Will be given as a global variable
 * to wordpress in order to easily add
 * own functionality and interact with our abstraction.
 */
class WPAward {
	private $db;
	private $db_table;

	function __construct( $db ) {
		$this->db = $db;
		$this->db_table = $this->db->prefix . "awards";
	}

	private function __auto_give_award( $award_id ) {
		return get_post_meta( $award_id, 'wap_auto_give', true );
	}

	/**
	 * Function that will mark an award as given to a user,
	 * which essentially means that we mark the "date_given" time with
	 * an actual date.
	 *
	 * Returns the return value of a `db->update` call
	 *
	 * @param int $user_id  - ID of the user that we are "awarding" the award to
	 * @param int $award_id - ID of the award that we are "awarding"
	 *
	 */
	function GiveAward( $user_id, $award_id ) {

		// Marks the award as given to the user, instead of just assigned to the user
		$award_given = $this->db->update(
			$this->db_table,
			[
				'date_given' => date('Y-m-d G:i:s')
			],
			[
				'user_id' => $user_id,
				'award_id' => $award_id
			],
			NULL,
			[
				'user_id' => '%d',
				'award_id' => '%d'
			]
		);

		return $award_given;
	}

	/**
	 * Function that marks an award as assigned to a user.
	 * We insert a new record into our awards table that relates the award to the user.
	 *
	 * We do check to see if there is an auto-assignment of the award before we finish up our function though.
	 *
	 * @param int $user_id  - ID of the user that we are "awarding" the award to
	 * @param int $award_id - ID of the award that we are "awarding"
	 */
	function AssignAward( $user_id, $award_id ) {

		$award_assigned = $this->db->insert(
			$this->db_table,
			[
				'user_id' => $user_id,
				'award_id' => $award_id,
			],
			[
				'user_id' => '%d',
				'award_id' => '%d'
			]
		);

		if ( ! $award_assigned )
		{
			return false;
		}

		if ( $this->__auto_give_award( $award_id ) )
		{
			$award_given = $this->GiveAward( $award_id, $user_id );
		}

		return true;
	}

	/**
	 * Removes awards from our database.
	 * If "$award_id" is null, then we are going to delete everything in the database with the specific "$user_id"
	 *
	 * @param int $user_id  - ID of the user that we are "awarding" the award to
	 * @param int $award_id - ID of the award that we are "awarding"
	 */
	function RemoveAward( $user_id, $award_id = NULL ) {

		$where_clause = [
			'user_id' => $user_id,
		];

		$where_format = [
			'user_id' => '%d'
		];

		// Add in our award id to the where clase if it is available. There's a chance that it isn't.
		if ( $award_id )
		{
			$where_clause['award_id'] = $award_id;
			$where_format['award_id'] = '%d';
		}

		$award_deleted = $this->db->delete(
			$this->db_table,
			$where_clause,
			$where_format
		);

		return $award_deleted;
	}

	/**
	 * Function that grabs as many awards assigned to the user as we can based on the parameters given.
	 * For example, if just a user_id is supplied, then we will return all of the awards with that user_id.
	 * If an award_id is supplied along with our user_id then we will probably get only one award. Hopefully
	 *
	 * @param int $user_id  - ID of the user that we are "awarding" the award to
	 * @param int $award_id - ID of the award that we are "awarding"
	 */
	function GetUserAward( $user_id, $award_id = NULL) {
		$query = "SELECT * FROM {$this->db_table}"
		$awards = $this->wp->get_results();
	}

	/**
	 * Compares $val_1 to $val_2 based on the operator given.
	 * @param mixed $val_1
	 * @param mixed $val_2
	 * @param string $op    - Operator.
	 */
	function ShouldApplyAward( $val_1, $val_2, $op ) {
		switch ( $op ) {
			case 'gt':
				return $val_1 > $val_2;
			case 'lt':
				return $val_1 < $val_2;
			case 'eq':
				return $val_1 === $val_2;
			case 'gteq':
				return $val_1 >= $val_2;
			case 'lteq':
				return $val_1 <= $val_2;
		}
	}
}
?>