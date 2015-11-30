<?php
/**
 * Podstawowa konfiguracja WordPressa.
 *
 * Ten plik zawiera konfiguracje: ustawień MySQL-a, prefiksu tabel
 * w bazie danych, tajnych kluczy i ABSPATH. Więcej informacji
 * znajduje się na stronie
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Kodeksu. Ustawienia MySQL-a możesz zdobyć
 * od administratora Twojego serwera.
 *
 * Ten plik jest używany przez skrypt automatycznie tworzący plik
 * wp-config.php podczas instalacji. Nie musisz korzystać z tego
 * skryptu, możesz po prostu skopiować ten plik, nazwać go
 * "wp-config.php" i wprowadzić do niego odpowiednie wartości.
 *
 * @package WordPress
 */

// ** Ustawienia MySQL-a - możesz uzyskać je od administratora Twojego serwera ** //
/** Nazwa bazy danych, której używać ma WordPress */
define('DB_NAME', 'ohif');

/** Nazwa użytkownika bazy danych MySQL */
define('DB_USER', 'ohifdb');

/** Hasło użytkownika bazy danych MySQL */
define('DB_PASSWORD', '0hifdb!23$56');

/** Nazwa hosta serwera MySQL */
define('DB_HOST', 'localhost');

/** Kodowanie bazy danych używane do stworzenia tabel w bazie danych. */
define('DB_CHARSET', 'utf8mb4');

/** Typ porównań w bazie danych. Nie zmieniaj tego ustawienia, jeśli masz jakieś wątpliwości. */
define('DB_COLLATE', '');

/**#@+
 * Unikatowe klucze uwierzytelniania i sole.
 *
 * Zmień każdy klucz tak, aby był inną, unikatową frazą!
 * Możesz wygenerować klucze przy pomocy {@link https://api.wordpress.org/secret-key/1.1/salt/ serwisu generującego tajne klucze witryny WordPress.org}
 * Klucze te mogą zostać zmienione w dowolnej chwili, aby uczynić nieważnymi wszelkie istniejące ciasteczka. Uczynienie tego zmusi wszystkich użytkowników do ponownego zalogowania się.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'cz+WZ:5sBg*c>^xvqr=p%/cX`4|Eql6Z8-BQhkseW{xeqOzCNk,W2^-rg65|?Q{Q');
define('SECURE_AUTH_KEY',  'v8pOP*m4fTh[t_XX 1HHG-<G+m5#xeUXeC5%Q+d;&c[wpkT;=e3ye6*-m[WDZW}|');
define('LOGGED_IN_KEY',    '@JHV25TbrdXW;zNkODpUmmOJvm}B%F4Irz{rw1lwgrsQFSJXD-Q}vYM->J2@|<2L');
define('NONCE_KEY',        'g^h{LMOPL7`cp=/aQAnu:}S&f,.%8$hFDs=U0MpkHB|BQ2k$GH}>36*;.?wJ+m(L');
define('AUTH_SALT',        '*Vr-hc_&V%pF-@VCiCTSy8p[u&Z-9;-d-2gOQ)#]*zLp_ZUSI ~=x97) EGoIj^S');
define('SECURE_AUTH_SALT', '&u`.px<e;J DO[Q#jyVE8n,!XQU2?a#<@GbbpJ^EM/Omm$[,%3>wDu}!l^u2{h@S');
define('LOGGED_IN_SALT',   'pOR.w8m4(vJsH|DK1akZ|CH5^M|7xe6vs$v$#-jpH<6g^kogRnc]^3&$#<MfJkY7');
define('NONCE_SALT',       '<wR3,l%#1 @MeR!WCnZyp~h!xl8 TBV#l&ekop}rF1vS)WF8M9[1:5&rp^h9JUMB');

/**#@-*/

/**
 * Prefiks tabel WordPressa w bazie danych.
 *
 * Możesz posiadać kilka instalacji WordPressa w jednej bazie danych,
 * jeżeli nadasz każdej z nich unikalny prefiks.
 * Tylko cyfry, litery i znaki podkreślenia, proszę!
 */
$table_prefix  = 'wp_';

/**
 * Dla programistów: tryb debugowania WordPressa.
 *
 * Zmień wartość tej stałej na true, aby włączyć wyświetlanie ostrzeżeń
 * podczas modyfikowania kodu WordPressa.
 * Wielce zalecane jest, aby twórcy wtyczek oraz motywów używali
 * WP_DEBUG w miejscach pracy nad nimi.
 */
define('WP_DEBUG', false);

/* To wszystko, zakończ edycję w tym miejscu! Miłego blogowania! */

/** Absolutna ścieżka do katalogu WordPressa. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Ustawia zmienne WordPressa i dołączane pliki. */
require_once(ABSPATH . 'wp-settings.php');
