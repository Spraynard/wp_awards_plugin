<?php
	/**
	 * Controller for handling Award Plugin level database operations.
	 */
	class Awards_DB {
		private $wpdb;

		function __construct() {
			global $wpdb;

			// Setting class specific wpdb from the global
			$this->wpdb = $wpdb;

			// Specific prefix naming for the awards table
			$this->tablename_awards = $wpdb->prefix . "awards";
		}

		private function get_db_version() {
			return $this->awards_db_version;
		}

		/** Function to handle plugin activation */
		static function awards_activate() {
			$charset_collate = $this->wpdb->get_charset_collate();

			$table_name = $this->wpdb->prefix . "awards";

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					title text NOT NULL,
					description text NOT NULL,
					date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
					date_modified datetime DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
					PRIMARY KEY  (id)
				) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // for dbDelta
			dbDelta( $sql );

			add_option( 'awards_db_version', 'Awards_DB::get_db_version' );
		}

		/** Function to handle plugin deactivation */
		static function awards_deactivate() {
			global $wpdb;

			$sql = "DROP TABLE IF EXISTS $this->tablename_awards";
			$this->wpdb->query( $sql );
			delete_option('awards_db_version');
		}

		/** Function to handle plugin uninstallation */
		static function awards_uninstall() {
			global $wpdb;

			$sql = "DROP TABLE IF EXISTS $this->tablename_awards";
			$this->wpdb->query( $sql );
			delete_option('awards_db_version');
		}

		/**
		 * Obtain all awards
		 */
		function GetAwards() {
			$sql = "SELECT id, title, description FROM $this->tablename_awards";

			return $this->wpdb->get_results($sql);
		}

		/**
		 * Obtain Award Information
		 * @param int $id - ID of the award
		 */
		function GetAward( $id, $output_type = OBJECT ) {
			$sql = "SELECT * FROM $this->tablename_awards WHERE id = %d";

			return $this->wpdb->get_row($this->wpdb->prepare($sql, $id), $output_type);
		}

		/**
		 * Update Award Information
		 * @param int $id - ID of the award
		 */
		function UpdateAward( $id ) {
			$sql = "SELECT * FROM $this->tablename_awards WHERE id = %d";

			return $this->wpdb->get_row($this->wpdb->prepare($sql, $id));
		}
	}
?>