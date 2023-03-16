=== Extend Search Block ===

Contributors:      webmandesign
Donate link:       https://www.webmandesign.eu/contact/#donation
Author URI:        https://www.webmandesign.eu
Plugin URI:        https://www.webmandesign.eu/portfolio/extend-search-block-wordpress-plugin/
Requires at least: 6.1
Tested up to:      6.1
Requires PHP:      7.0
Stable tag:        1.0.0
License:           GNU General Public License v3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Tags:              webman, webman design, blocks, block editor, block, tha, theme hook alliance, hooks, actions

Extending core Search block with search modifier fields.


== Description ==

Extending core Search block with search modifier fields.

= What problem does it solve? = @TODO

❓ _Do you want to execute PHP code in the page or post content?_

Extend Search Block plugin provides **Action hook** block solving these cases!

= Got a question or suggestion? =

In case of any question or suggestion regarding this plugin, feel free to ask at [support section](https://wordpress.org/support/plugin/extend-search-block/), or at [GitHub repository issues](https://github.com/webmandesign/extend-search-block/issues).


== Installation ==

1. Unzip the plugin download file and upload `extend-search-block` folder into the `/wp-content/plugins/` directory.
2. Activate the plugin through the *"Plugins"* menu in WordPress.
3. Plugin works immediately after activation by adding new settings options to WordPress native "Search" block in block editor.


== Frequently Asked Questions == @TODO

= How does it work? =

1. Insert an **Action hook** block into your page/post content (or into Site Editor) where you want to execute your PHP code.
2. Select an action hook name to be executed at the place.
3. Save the post/page/Site Editor.
4. In your theme's `functions.php` file add your PHP code to execute, such as: `add_action( 'action_hook_name_here', function() { echo 'Hello world!'; } );`
5. Watch your code appear on your website front-end.


== Screenshots ==

1. Preview of the block functionality


== Changelog ==

Please see the [`changelog.md` file](https://github.com/webmandesign/extend-search-block/blob/master/changelog.md) for details.


== Upgrade Notice ==

= 1.0.0 =
Initial release.
