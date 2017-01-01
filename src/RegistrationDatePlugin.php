<?php

namespace Corphi\WordPress;

/**
 * A small plugin adding the registration date column to the users table.
 * 
 * @author Philipp Cordes <pc@irgendware.net>
 * @license GPL-2.0+
 */
class RegistrationDatePlugin
{
	/**
	 * Shortcut method for registering a method as action or filter.
	 *
	 * @param string $actionOrFilter
	 * @param string $method
	 * @param int    $priority
	 * @param int    $acceptedArgs
	 *
	 * @return void
	 */
	protected function addHookMethod( $actionOrFilter, $method, $priority = 10, $acceptedArgs = 99 )
	{
		add_filter( $actionOrFilter, [ $this, $method ], $priority, $acceptedArgs );
	}

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
	 *
	 * @return string[]
	 */
	public function filterColumns( array $columns )
	{
		$columns['user_registered'] = __( 'Registration date', 'registration-date' );

		return $columns;
	}

	/**
	 * Make the column sortable.
	 *
	 * @param string[] $sortableColumns
	 *
	 * @return string[]
	 */
	public function filterSortableColumns( array $sortableColumns )
	{
		$sortableColumns['user_registered'] = 'user_registered';

		return $sortableColumns;
	}

	/**
	 * Output the date.
	 *
	 * @param string $output
	 * @param string $columnName
	 * @param int    $userId
	 *
	 * @return string
	 */
	public function filterColumnOutput( $output, $columnName, $userId )
	{
		if ( 'user_registered' !== $columnName ) {
			return $output;
		}

		$timestamp = strtotime( get_userdata( $userId )->user_registered );

		return sprintf(
			'<time datetime="%s" title="%s, %s">%s</time>',
			esc_attr( date( \DateTime::RFC3339, $timestamp ) ),
			esc_attr( date_i18n( get_option( 'date_format' ), $timestamp ) ),
			esc_attr( date_i18n( get_option( 'time_format' ), $timestamp ) ),
			esc_html( sprintf( __( '%s ago', 'registration-date' ), human_time_diff( $timestamp ) ) )
		);
	}
}
