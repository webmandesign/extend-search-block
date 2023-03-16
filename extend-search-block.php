<?php
/**
 * Plugin Name:  Extend Search Block
 * Plugin URI:   https://www.webmandesign.eu/portfolio/extend-search-block-wordpress-plugin/
 * Description:  Extending core Search block with search modifier fields.
 * Version:      1.0.0
 * Author:       WebMan Design, Oliver Juhas
 * Author URI:   https://www.webmandesign.eu/
 * License:      GNU General Public License v3
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:  extend-search-block
 * Domain Path:  /languages
 *
 * Requires PHP:       7.0
 * Requires at least:  6.1
 *
 * GitHub Plugin URI:  https://github.com/webmandesign/extend-search-block
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/extend-search-block
 * @link  https://www.webmandesign.eu
 *
 * @package  Extend Search Block
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Constants.
define( 'EXTEND_SEARCH_BLOCK_VERSION', '1.0.0' );
define( 'EXTEND_SEARCH_BLOCK_FILE', __FILE__ );
define( 'EXTEND_SEARCH_BLOCK_PATH', plugin_dir_path( EXTEND_SEARCH_BLOCK_FILE ) ); // Trailing slashed.
define( 'EXTEND_SEARCH_BLOCK_URL', plugin_dir_url( EXTEND_SEARCH_BLOCK_FILE ) ); // Trailing slashed.

// Load the functionality.
require_once EXTEND_SEARCH_BLOCK_PATH . 'includes/Autoload.php';
WebManDesign\Block\Mod\Search\Block::init();
