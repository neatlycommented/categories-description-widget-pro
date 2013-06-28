<?php
/*
Plugin Name: Categories Widget with Descriptions Pro
Plugin URI: https://neatlycommented.com
Description: Creates a widget that displays your categories with description in your blog sidebar. Pro version contains additional features including ability to select taxonomy and/or parent category. Free version available at: https://github.com/zoerooney/Categories-Descriptions-Widget
Version: 1.0
Author: Zoe Rooney
Author URI: http://zoerooney.com
License: GPL2

Copyright 2013 Zoe Rooney (hello@zoerooney.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Create Walker class
=============================================*/

class category_list_with_description extends Walker_Category {
   function start_el(&$output, $category, $depth, $args) {
      extract($args);
      
      $cat_name = esc_attr( $category->name);
      $cat_name = apply_filters( 'list_cats', $cat_name, $category );
      
      $link = '<a href="' . get_category_link( $category->term_id ) . '" ';
      
      if ( $use_desc_for_title == 0 || empty($category->description) )
         $link .= 'title="' . sprintf(__( 'View all posts filed under %s' ), $cat_name) . '"';
      else
         $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
      $link .= '>';
      
      $link .= $cat_name;
      if(!empty($category->description)) {
         $link .= ' <span>'.$category->description.'</span>';
      }
      $link .= '</a>';
      
      if ( isset($show_count) && $show_count )
         $link .= ' (' . intval($category->count) . ')';
      
      $output .= '<li class="cat-item cat-item-' . $category->term_id;
      if ( ( is_category() || is_tax() || is_tag() ) ) {
      	$current_term_object = get_queried_object();
      	if ( $category->term_id == $current_category )
      		$output .= ' current-category';
      }
      $output .= '">';
      $output .= $link;
      $output .= '</li>';
	}
}

/* Adds our widget
=============================================*/

class neatly_categories_description extends WP_Widget {

    function neatly_categories_description() {
        $widget_ops = array(
            'classname'=>'neatly-categories', // class that will be added to li element in widgeted area ul
            'description'=> __('Display categories with description') // description displayed in admin
            );
        $this->WP_Widget('neatly_categories_description', __('Categories with Description'), $widget_ops, $control_ops); // Name in  the control panel
    }
	
	/* Our arguments
	=============================================*/
		
    function widget($args, $instance) {
            extract($args);
			
			$title = $instance['title'];
			$taxonomy = $instance['taxonomy'];
			$child_of = $instance['child_of']; 
			$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : 'name';
			$order = isset( $instance['order'] ) ? $instance['order'] : 'ASC';
			$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : true;
				
			/* Outputting our widget on the front end
			=============================================*/
			echo '<style></style>';
				  
            echo $before_widget . $before_title . $title . $after_title; // widget title
  			
  			echo '<ul>';
  			$neatly_category_walker = new category_list_with_description();
  			wp_list_categories(array(
  				'orderby' => $orderby,
  				'order' => $order,
  				'hide_empty' => $hide_empty,
  				'title_li' => '',
  				'taxonomy' => $taxonomy,
  				'child_of' => $child_of,
  				'walker' => $neatly_category_walker
  			));
  			echo '</ul>';
            
            echo $after_widget; // ends the widget
        }
        	  
	
	/* Saving updated information
	=============================================*/
	
    function update( $new_instance, $old_instance ) {
        $new_instance = (array) $new_instance;
        $instance = array( 'hide_empty' => 0 );
        foreach ( $instance as $field => $val ) {
        	if ( isset($new_instance[$field]) )
        		$instance[$field] = 1;
        }
        
        $instance['title'] = strip_tags($new_instance['title']);
        
        $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
        
        $instance['child_of'] = strip_tags($new_instance['child_of']);
       
        $instance['orderby'] = 'name';
        if ( in_array( $new_instance['orderby'], array( 'name', 'ID', 'count' ) ) )
        	$instance['orderby'] = $new_instance['orderby'];
       
        $instance['order'] = 'ASC';
        if ( in_array( $new_instance['order'], array( 'ASC', 'DESC' ) ) )
        	$instance['order'] = $new_instance['order'];
          
        return $instance;
    }
        
	
	/* The widget configuration form
	=============================================*/
	
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'taxonomy' => 'category', 'child_of' => '', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true ) ); 
        $title = strip_tags($instance['title']);
        $taxonomy = strip_tags($instance['taxonomy']);
        $child_of = strip_tags($instance['child_of']);
        
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><em>Use the following options to customize the display:</em></p>
		
		<p style="border-bottom:4px double #eee;padding: 0 0 10px;">
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>">Order Categories By</label>
			<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>><?php _e('Category Name'); ?></option>
				<option value="ID"<?php selected( $instance['orderby'], 'ID' ); ?>><?php _e('Cateory ID'); ?></option>
				<option value="count"<?php selected( $instance['orderby'], 'count' ); ?>><?php _e( 'Number of Posts' ); ?></option>
			</select>
		</p>
		<p style="border-bottom:4px double #eee;padding: 0 0 10px;">
			<label for="<?php echo $this->get_field_id( 'order' ); ?>">Display in</label>
			<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
				<option value="ASC"<?php selected( $instance['order'], 'ASC' ); ?>><?php _e('Ascending Order'); ?></option>
				<option value="DESC"<?php selected( $instance['order'], 'DESC' ); ?>><?php _e('Descending Order'); ?></option>
			</select>
		</p>
		<p style="border-bottom:4px double #eee;padding: 0 0 10px;">
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">Hide categories with no posts?
			<input id="<?php echo $this->get_field_id( 'hide_empty'); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" <?php checked($instance['hide_empty'], true) ?>  type="checkbox" /></label><br><br>
		</p>
		<p><em>Additional options for displaying selected categories &amp; custom taxonomies:</em></p>
	    <p style="border-bottom:4px double #eee;padding: 0 0 10px;">
		    <label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy (default is category):'); ?></label>
		    <input class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" type="text" value="<?php echo esc_attr($taxonomy); ?>" />
	    </p>
	    <p style="border-bottom:4px double #eee;padding: 0 0 10px;">
	        <label for="<?php echo $this->get_field_id('child_of'); ?>"><?php _e('Enter a category ID to only show children of that category:'); ?></label>
	        <input class="widefat" id="<?php echo $this->get_field_id('child_of'); ?>" name="<?php echo $this->get_field_name('child_of'); ?>" type="text" value="<?php echo esc_attr($child_of); ?>" />
	    </p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("neatly_categories_description");')); 


/* Create a shortcode version
=============================================*/
// [neatly_categories orderby="name" order="ASC" hide_empty="1" taxonomy="category" child_of=""]

function neatly_categories_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'title_text' => 'Categories',
		'orderby' => 'name',
		'order' => 'ASC',
		'taxonomy' => 'category',
		'child_of' => 0,
		'hide_empty' => true
	), $atts ) );
	
	ob_start();
	

	echo '<div class="neatly-categories neatly-categories-shortcode"><h3>' . $title_text . '</h3>';
	
		echo '<ul>';
		$neatly_category_walker = new category_list_with_description();
		wp_list_categories(array(
			'orderby' => $orderby,
			'order' => $order,
			'hide_empty' => $hide_empty,
			'title_li' => '',
			'taxonomy' => $taxonomy,
			'child_of' => $child_of,
			'walker' => $neatly_category_walker
		));
		echo '</ul>'; 
		
	echo '</div>';
	
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}	
add_shortcode( 'neatly_categories', 'neatly_categories_shortcode' );

?>