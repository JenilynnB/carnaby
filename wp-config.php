<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'carnabyu_wor7091');

/** MySQL database username */
define('DB_USER', 'carnabyu_wor7091');

/** MySQL database password */
define('DB_PASSWORD', 'TiUSfCzKjbRE');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY', 'R-G(Yn!*z?i!gD=@oemHV@A@;;GzXv!DakwkdeO[-xQZA;Rs;C+o;|[KXI_>mkfTNzb(_A{QfOVo^Gg!<GMy@>+vpy_zIpUvNa!t^amF=Ur-Xafc^FR{iW)?X^(URPlp');
define('SECURE_AUTH_KEY', '|^vWkWU}<fHswJ&nKIK$^+)CY{WJ;ZD(ZOXIjN{*hBpDoo]Bb=eZJD)x%eZUNK-XwAIN}<$PrTmL&Qj_KrF=cjqa)C-l)A)+C}PxXk{WHE]__}C!U?cnXJ{%fkquZxK{');
define('LOGGED_IN_KEY', '>FbHE-wO>$RpfgP}?vh$&d)tH^sKgYLz_Rihs]dPD=fFy[(Cv=lnZ|_^Du-FA|+R?KVH=SGY+ph]GLF|+RxzFZhrsd]yV^}p+dNNR*QCABcY-pmT>TB?Wy|^PVc/hGmA');
define('NONCE_KEY', 'w/o;mp^oPVp*&DDtde_?GeIYAM(b*((wQR>|^Yo$ty^%DZi?apb)oN|_{HyFv;$}bsk+-eSR{EODD$DZMHlj@;-ZAyFMni%R^&}erc&-yvfBhL({S-Hm&@zbA%@)MFxx');
define('AUTH_SALT', '/P*FDGaCI)T-G>;mCyEDmK+Qc{r(N?hnlAG!xgh_+>%PpNA$Lu}sXmYu/o^a_gN<{zeBjxEz(^%?J;U$xMSojY=Wf&PVZNPS)i<-i^a)i;bnGqYiEKKVQ@]DaNqwm!dg');
define('SECURE_AUTH_SALT', 'OEttFrn!zw>U_qTMtFO@P&wx+ra>Teh&U@LRv=Y}EJy^$xqS*VvLe%e]b*F{ClT{t}NDc]d_C&V/s%oT@W{WIqQg<awn=Kkacz<kGJalkzpkEb^g)+C=aYa)@}t+&+;/');
define('LOGGED_IN_SALT', 'QrsRSzK>?MjRs?s=UYO%DQ|fsdlBtgrgUzUxE^=|zX}$bPz^rCWk-dyHZef[S<G|r{*Ra!tO)eLL=?S{NYKp+lmc_C/kO-R]mj@KIorctQn^_B$>Cgq_J*xXnR;aztmg');
define('NONCE_SALT', '%MtGUebnPs+iGJ%VYrfmC--M>k$[Kv<)xDj^|/X(RZLcDY$t*jONt={lPNC&]ESUe^*j}uG%rLrwZ@uCtG*pdm[Ojz(_OfNE*!t;z&iJB_Da*iw)$BstAqluV|nF)lDW');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_opba_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
