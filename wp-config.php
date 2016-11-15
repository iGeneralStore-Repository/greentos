<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'localhost_greentos');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'L8nTrw7jCr$]5=PebLk@Eq-|rFhP]3=I||S#T|1+!`|@b_Kjk:>D/,fU|s9J^5P(');
define('SECURE_AUTH_KEY',  '=ktgy;=$B[a}0cp{CQ9|KoTT[y d#<78S:v 8L>7jj#%P:b|tx`cjU0+{3^cYE4+');
define('LOGGED_IN_KEY',    'A !+vjoO!y w@gZY-#IZU87|9$%2LQ&~}2}^X^:3+5p!]oJyuKNlI*+cbXKc]`_6');
define('NONCE_KEY',        '}~a=pKUg#9g(mJ%Ed<8a)K}ytXZ~l1ly08PYm,iZuD~q^;||0F_q(+[=X@8AU*L@');
define('AUTH_SALT',        'c_?Jt-PTxW7:>>O5txP-]I*Ue7OA/_R11BI&:|E{-vf&0z<p1Azf8-o+>|6J.4{D');
define('SECURE_AUTH_SALT', 'L+=3V[;06P/8{zA>CK;SiwXpTyxfDpK~:,;+tv?&$q24)wj-kn(0`[-[dOcrOCH8');
define('LOGGED_IN_SALT',   '5EM[-]8I@v-TJ].wX*LZ]RkHv_54@isP$1vc16-A[km|JJZBrK]nRG,RwA=rVQl+');
define('NONCE_SALT',       '38W~T#zx oLQqY+bI0qn!~w|lI8MEj#t^No2`%GJAB!p8OdtLj3,(o+HB6Z A/Nc');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
