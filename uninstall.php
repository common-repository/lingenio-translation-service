<?php

function pg_uninstall_plugin()
{
	//if uninstall not called from WordPress exit
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit ();
}