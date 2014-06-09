<?php
/**
 * Plugin Name: IndieWeb Custom Taxonomy
 * Plugin URI: http://tiny.n9n.us
 * Description: Adds Semantic Functionality to Posts
 * Version: 0.01
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * License: CC0
 */

// Register Kind to Distinguish the Types of Posts

require_once( plugin_dir_path( __FILE__ ) . '/WDS_Taxonomy_Radio.class.php');
require_once( plugin_dir_path( __FILE__ ) . '/WDS_Taxonomy_Radio_Walker.php');
// Add Kind Post Metadata
require_once( plugin_dir_path( __FILE__ ) . '/kind-postmeta.php');


add_action( 'init', 'register_taxonomy_kind' );

function register_taxonomy_kind() {

    $labels = array( 
        'name' => _x( 'Kinds', 'kind' ),
        'singular_name' => _x( 'Kind', 'kind' ),
        'search_items' => _x( 'Search Kinds', 'kind' ),
        'popular_items' => _x( 'Popular Kinds', 'kind' ),
        'all_items' => _x( 'All Kinds', 'kind' ),
        'parent_item' => _x( 'Parent Kind', 'kind' ),
        'parent_item_colon' => _x( 'Parent Kind:', 'kind' ),
        'edit_item' => _x( 'Edit Kind', 'kind' ),
        'update_item' => _x( 'Update Kind', 'kind' ),
        'add_new_item' => _x( 'Add New Kind', 'kind' ),
        'new_item_name' => _x( 'New Kind', 'kind' ),
        'separate_items_with_commas' => _x( 'Separate kinds with commas', 'kind' ),
        'add_or_remove_items' => _x( 'Add or remove kinds', 'kind' ),
        'choose_from_most_used' => _x( 'Choose from the most used kinds', 'kind' ),
        'menu_name' => _x( 'Kinds', 'kind' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'kind', array('post'), $args );
}

// Sets up some starter terms...unless terms already exist 
// or any of the existing terms are defined
function kind_defaultterms () {

    // see if we already have populated any terms
    $kinds = get_terms( 'kind', array( 'hide_empty' => false ) );
     // if no terms then lets add our terms
    if( empty($kinds) ) {
	if (!term_exists('Like', 'kind')) {
	      wp_insert_term('Like', 'kind', 
		array(
   		 	  'description'=> 'Like',
    			  'slug' => 'like',
		     ) );

            }  
        if (!term_exists('Reply', 'kind')) {
              wp_insert_term('Reply', 'kind',
                array(
                          'description'=> 'Reply',
                          'slug' => 'reply',
                     ) );

            }
        if (!term_exists('RSVP', 'kind')) {
              wp_insert_term('RSVP', 'kind',
                array(
                          'description'=> 'RSVP for Event',
                          'slug' => 'rsvp',
                     ) );

            }
        if (!term_exists('Repost', 'kind')) {
              wp_insert_term('Repost', 'kind',
                array(
                          'description'=> 'Repost',
                          'slug' => 'repost',
                     ) );

            }
        if (!term_exists('Bookmark', 'kind')) {
              wp_insert_term('Bookmark', 'kind',
                array(
                          'description'=> 'Sharing a Link',
                          'slug' => 'bookmark',
                     ) );

            }



 	}
}

add_action( 'init', 'kind_defaultterms'); 

if(get_option('indieweb_taxonomy_multikind')!="true"){
	$kind_mb = new WDS_Taxonomy_Radio( 'kind' );
}

add_filter('post_link', 'kind_permalink', 10, 3);
add_filter('post_type_link', 'kind_permalink', 10, 3);
 
function kind_permalink($permalink, $post_id, $leavename) {
    if (strpos($permalink, '%kind%') === FALSE) return $permalink;
     
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;
 
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'kind');   
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'standard';
 
    return str_replace('%kind%', $taxonomy_slug, $permalink);
}   


function indieweb_taxonomy_options()
{
?>
    <div class="wrap">
        <h2>Indieweb Taxonomy Options</h2>
        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options') ?>
            <p><strong>Multikind:</strong><br />
                <input type="radio" name="indieweb_taxonomy_multikind" size="45" value="false"  <?php echo get_option('indieweb_taxonomy_multikind')!="true"?'checked="checked"':''; ?>/> Disabled<br>
				<input type="radio" name="indieweb_taxonomy_multikind" size="45" value="true" <?php echo get_option('indieweb_taxonomy_multikind')=="true"?'checked="checked"':''; ?>/> Enabled
            </p>
            <p><input type="submit" name="Submit" value="Store Options" /></p>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="indieweb_taxonomy_multikind" />
        </form>
    </div>
<?php
}

function add_indieweb_taxonomy_options_to_menu(){
	add_options_page( 'Indieweb Taxonomy Options', 'Indieweb Taxonomy Options', 'manage_options', 'functions', 'indieweb_taxonomy_options');
}

add_action('admin_menu', 'add_indieweb_taxonomy_options_to_menu');



?>
