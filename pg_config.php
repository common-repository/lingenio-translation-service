<?php

/**
 * The base configurations of the Polyglotter.
 *
 * 
 */

// ** Server Settings, to connect to lingenio ** //
/** Hostname and Quota of LTS */
define('PG_HOST', 'cluster1.wwwtranslate.eu');
define('PG_QUOTA_HOST', 'quota.wwwtranslate.eu');
/** Path to json Api */
define('PG_JSON', 'lts/json');
/** Function, i.e. translate */
define('PG_FUNC', 'translateHTML');
define('PG_QUOTA_FUNC', 'getQuota');
/** Port of LTS service */
define('PG_PORT', 8129);
define('PG_QUOTA_PORT', 5000);

// ** Translation settings ** //
/** Timelimit(ms) for translating a sentence */
define('PG_TIMELIMIT', 2500);
/** Timeout(s) for http request */
define('PG_CURLLIMIT', '20');
/** Alternative translations */
define('PG_ALTCOUNT', 4);

// ** Debugging ** //
/** Write querys to PLUGIN_DIR/pg_log.txt */
define('PG_DEBUG', false);
/** Debug file */
define('PG_DEBUG_FILE', dirname(__FILE__).'/pg_log.txt');
