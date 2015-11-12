<?php

namespace Corphi\WordPress;

use Shy\WordPress\Plugin;



/**
 * A small plugin adding the registration date column to the users table.
 * 
 * @author Philipp Cordes <pc@irgendware.net>
 * @license GPL-2.0+
 */
class RegistrationDatePlugin extends Plugin
{
	public function __construct()
	{
		foreach ( [
			'manage_users_columns'          => 'filterColumns',
			'manage_users_sortable_columns' => 'filterSortableColumns',
			'manage_users_custom_column'    => 'filterColumnOutput',
		] as $filter => $method ) {
			$this->addHookMethod( $filter, $method );
		}
	}


	/**
	 * Add the column.
	 * 
	 * @param string[] $columns
	 * @return string[]
	 */
	public function filterColumns( array $columns )
	{
		$columns['registration_date'] = __( 'Registration date', 'registration-date' );

		return $columns;
	}

	/**
	 * Make the column sortable.
	 * 
	 * @param string[] $sortableColumns
	 * @return string[]
	 */
	public function filterSortableColumns( array $sortableColumns )
	{
		$sortableColumns['registration_date'] = 'registration_date';

		return $sortableColumns;
	}

	/**
	 * Output the date.
	 * 
	 * @param string $output
	 * @param string $columnName
	 * @param int    $userId
	 * @return string
	 */
	public function filterColumnOutput( $output, $columnName, $userId )
	{
		if ( 'registration_date' !== $columnName ) {
			return $output;
		}

		$timestamp = strtotime( get_userdata( $userId )->user_registered );

		return sprintf(
			'<abbr title="%s">%s</abbr>',
			date_i18n( get_option( 'date_format' ), $timestamp ),
			sprintf( __( '%s ago', 'registration-date' ), human_time_diff( $timestamp ) )
		);
	}
}
