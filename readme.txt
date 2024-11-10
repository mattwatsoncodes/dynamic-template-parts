=== Dynamic Template Parts ===
Contributors:      mattwatsoncodes
Tags:              template, block, editor, switcher, custom-templates
Requires at least: 5.8
Tested up to:      6.6
Stable tag:        0.0.1
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

**Maximise your site's flexibility** — swap Template Parts dynamically for each post, saving time and enhancing creativity.

== Description ==

**Are you tired of juggling multiple templates for every variation of your site? Dynamic Template Parts is here to revolutionize your workflow.**

Dynamic Template Parts extends the WordPress block editor, allowing you to dynamically switch and customise Template Parts based on your content. No more creating endless templates for every possible combination of headers, footers, and sidebars. With this plugin, you can effortlessly swap out individual Template Parts on a post-by-post basis, building your templates on the fly.

Experience a new level of flexibility in designing your site layouts. The intuitive UI comes with previews, so you can see exactly what you're swapping before you make the change. Whether you're working with posts, pages, or custom post types, Dynamic Template Parts streamlines your editing process, saving you time and hassle.

**Key Features:**

- **Dynamic Template Part Swapping:** Swap Template Parts like headers, footers, and sidebars directly within the block editor.
- **Flexible Swapping Options:** Choose to swap Template Parts of the same type, any type, or curate specific parts for swapping.
- **Template Part Previews:** See what each Template Part looks like before you apply it.
- **Supports All Content Types:** Works seamlessly with posts, pages, and custom post types.
- **User-Friendly Interface:** Easy to use, no coding required.

**Get Started:**

To implement an alternative Header Template Part using the Full Site Editor, please follow the example workflow below:

1.	**Create an Alternative Header Template Part:** Design and save a new Header Template Part within the Full Site Editor to serve as an alternative to your default header.
2.	**Edit the Single Posts Template:** Navigate to the Single Posts template and select the Header Template Part, and view the block sidebar.
3.	**Enable the Dynamic Template Part Option:** In the Template Part attributes panel, enable the Dynamic Template Part setting.
4.	**Configure Swapping Preferences (Optional):** Adjust your swapping preferences according to your requirements.
5.	**Edit a Post and Access the Dynamic Template Part Sidebar:** Open a post for editing and select the Dynamic Template Part sidebar from the editor interface.
6.	**Select the Alternative Header Template Part:** Choose the alternative Header Template Part you created to replace the default header in this specific post.
7.	**Save and Preview the Post:** Save your changes and preview the post to verify that the alternative header is displayed correctly.
8.	**Confirm Default Header on Other Posts:** Ensure that all other posts without specific configurations continue to display the default header as intended.

Unlock the power of dynamic templating and take control of your site's design like never before!

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Frequently Asked Questions ==

= How do I enable dynamic Template Parts? =

In the template editor, select a Template Part and enable the 'Dynamic Template Part' option in its attributes. Choose your swapping options and save the template.

= Can I limit which Template Parts can be swapped? =

Yes! You can curate specific Template Parts that can be swapped. When enabling the 'Dynamic Template Part' option, select the Template Parts you want to make available for swapping.

= Does this plugin work with custom post types? =

Absolutely. Dynamic Template Parts works with posts, pages, and any custom post types you have.

= Can I swap Template Parts of different types? =

Yes, you have the option to allow swapping with Template Parts of the same type (Header, Footer, Generic), any type, or a curated list of types.

= How are user permissions managed? =

By default, any user with the `edit_posts` capability can switch template parts, which typically includes Authors and above. If you wish to restrict this ability to certain user roles, you can use the `dynamic_template_parts_user_can_switch` filter.

**Example:**

```
add_filter( 'dynamic_template_parts_user_can_switch', function( $permission ) {
    return current_user_can( 'manage_options' );
} );
````

= What happens if I deactivate the plugin? =

If you deactivate the plugin, your content will revert to using the default template parts defined in your theme. Your content remains safe, and you can reactivate the plugin anytime to restore dynamic functionality.

= Is this plugin compatible with my theme? =

Dynamic Template Parts is designed to work with any Full Site Editing (FSE) theme that utilizes block templates and template parts. It enhances the existing functionality of FSE themes without requiring theme modifications.

== Screenshots ==

1.	**Select a Template Part in the Template Editor:** Navigate to the Template Editor and select a Template Part you wish to edit.
2.	**Enable the Dynamic Template Part Option:** In the Template Part attributes panel, enable the Dynamic Template Part setting.
3.	**Choose Swapping Preferences:** Configure your swapping preferences by selecting whether to swap with the same type, any type, or curated Template Parts.
4.	**Edit a Post and Access the Dynamic Template Part Sidebar:** While editing a post, open the Dynamic Template Part sidebar from the editor interface.
5.	**View Available Template Parts with Previews, and Select an Alternative Template Part:** Browse through the available Template Parts, complete with live previews to assist in your selection. Choose the alternative Template Part you wish to substitute in place of the default.
6.	**Save and View the Post with Swapped Template Parts:** Save your changes and preview the post to see your customised Template Parts in action.

== Changelog ==

= 0.0.1 =
* Initial Preview Release

== Upgrade Notice ==

= 1.0.0 =
Welcome to the first release of Dynamic Template Parts! Enjoy dynamic control over your templates.

== Requirements ==

* WordPress version 5.8 or higher.
* PHP version 7.4 or higher.

== Known Issues ==

There are no known issues at this time.
