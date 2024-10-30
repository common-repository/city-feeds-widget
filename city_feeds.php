<?php
/*
==============================================================================

 City Feeds Widget for Wordpress
 
 Copyright (C) 2011 Eric Falkner

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 For a copy of the GPLv2 licence please visit:
 http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 
==============================================================================

 I wrote this plugin to integrate our plaza posts into our main
 website.  I hope it blesses your organization.  If you have any
 ideas for future releases, please email me at efalkner@yahoo.com
 
 Eric
 
==============================================================================
 Info for WordPress:
==============================================================================

Plugin Name: City Feeds Widget
Plugin URI: http://ericfalkner.com/cityfeeds
Description: Shows the information for Zondervan onthecity pages
Author: Eric Falkner
Version: 1.2.1
Author URI: http://ericfalkner.com/
*/

/**
 * City Feeds Widget class.
 */
class CityfeedsWidget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function CityfeedsWidget() {
		//Widget settings.
		$widget_ops = array( 'classname' => 'cityfeeds', 'description' => 'Imports the information from a Zondervan onthecity page' );

		//Widget control settings.
		$control_ops = array( 'width' => 300, 'height' => 450 );

		//Create the widget.
		parent::WP_Widget( false, 'TheCity Feeds', $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) 
	{
		extract( $args );
		extract( $instance );
		
        $title = apply_filters('widget_title', $instance['title']);

		/* Before widget (defined by themes). */
		echo $before_widget;
		
		/* Before title (defined by themes). */
		if ( !empty( $title ) ) 
		{ 
			echo $before_title . $title . $after_title;
		}
		
		//output the temporary container
		//ajax-loader.gif files can be generated at http://ajaxload.info/
		$icon_print = ($icon == 'default') ? 'ajax-loader.gif' : 'ajax-loader-'.$icon.'.gif';
		echo '<div id="city-content"><img src="'.site_url() . '/wp-content/plugins/city-feeds-widget/'.$icon_print.'" alt="Loading" />&nbsp;&nbsp;Loading content....</div>';

		$instance_serial  = 'url='.$url.'&age='.$age.'&limit='.$limit.'&sort='.$sort.'&chars='.$chars.'&cf_title_type='.$cf_title_type;
		$instance_serial .= '&date_format='.get_option('date_format');
		$instance_serial .= ($cf_topics == 1) ? '&cf_topics=1' : '&cf_topics=0';
		$instance_serial .= ($cf_events == 1) ? '&cf_events=1' : '&cf_events=0';
		$instance_serial .= ($cf_prayers == 1) ? '&cf_prayers=1' : '&cf_prayers=0';
		$instance_serial .= ($cf_needs == 1) ? '&cf_needs=1' : '&cf_needs=0';
		$instance_serial .= ($cf_albums == 1) ? '&cf_albums=1' : '&cf_albums=0';
		
//{ name: "John", time: "2pm" }
?>

<script type="text/javascript">

function loadXMLDoc(url, instance)
{
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else { // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
//alert(xmlhttp.responseText);
		    document.getElementById("city-content").innerHTML=xmlhttp.responseText;
	    }
	}
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(instance);
}

var url = "<?php echo site_url(); ?>/wp-content/plugins/city-feeds-widget/city_data_import.php";
var instance = '<?php echo $instance_serial; ?>';

loadXMLDoc(url, instance);

</script>
<?php

		//After widget (defined by themes).
		echo $after_widget;
	}


/**
 * Update the widget settings. (see widgets.php for instructions)
 */
	function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url']   = $new_instance['url'];
		$instance['age'] = $new_instance['age'];
		$instance['limit'] = $new_instance['limit'];
		$instance['sort'] = $new_instance['sort'];
		$instance['chars'] = $new_instance['chars'];
		$instance['icon'] = $new_instance['icon'];
		$instance['cf_title_type'] = $new_instance['cf_title_type'];
		$instance['cf_topics'] = $new_instance['cf_topics'];
		$instance['cf_events'] = $new_instance['cf_events'];
		$instance['cf_prayers'] = $new_instance['cf_prayers'];
		$instance['cf_needs'] = $new_instance['cf_needs'];
		$instance['cf_albums'] = $new_instance['cf_albums'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. 
	 */
	function form( $instance ) 
	{
		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => '', 
			'code' => '', 
			'age' => 'all', 
			'limit' => '10',
			'sort' => 'creation_desc', 
			'icon' => 'ajax-loader.gif', 
			'cf_title_type' => 'yes', 
			'cf_topics' => 1
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title: </label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
		</p>

		<!-- Feed URL: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>">City URL ( ______.onthecity.org ): </label>
			<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:95%;" />
		</p>
		
		<!-- Limit Feed Item Ages: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'age' ); ?>">Show Feeds Newer Than:</label>
			<select id="<?php echo $this->get_field_id( 'age' ); ?>" name="<?php echo $this->get_field_name( 'age' ); ?>" >
				<option value="all"<?php echo ($instance['age'] == 'all') ? ' selected' : '' ;?>>Show All</option>				
				<option value="1 day"<?php echo ($instance['age'] == '1 day') ? ' selected' : '' ;?>>1 Day Old</option>
				<option value="3 days"<?php echo ($instance['age'] == '3 days') ? ' selected' : '' ;?>>3 Days Old</option>
				<option value="1 week"<?php echo ($instance['age'] == '1 week') ? ' selected' : '' ;?>>1 Week</option>
				<option value="2 weeks"<?php echo ($instance['age'] == '2 weeks') ? ' selected' : '' ;?>>2 Weeks</option>
				<option value="3 weeks"<?php echo ($instance['age'] == '3 weeks') ? ' selected' : '' ;?>>3 Weeks</option>
				<option value="1 month"<?php echo ($instance['age'] == '1 month') ? ' selected' : '' ;?>>1 Month</option>
				<option value="2 months"<?php echo ($instance['age'] == '2 months') ? ' selected' : '' ;?>>2 Months</option>
				<option value="3 months"<?php echo ($instance['age'] == '3 months') ? ' selected' : '' ;?>>3 Months</option>
				<option value="6 months"<?php echo ($instance['age'] == '6 months') ? ' selected' : '' ;?>>6 Months</option>
				<option value="1 year"<?php echo ($instance['age'] == '1 year') ? ' selected' : '' ;?>>1 Year</option>
			</select>			
		</p>

		<!-- Max Feeds: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">Max Feeds to Show:</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" style="width:20%;" />
		</p>

		<!-- Max characters: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'chars' ); ?>">Max Body Characters to Show:</label>
			<input id="<?php echo $this->get_field_id( 'chars' ); ?>" name="<?php echo $this->get_field_name( 'chars' ); ?>" value="<?php echo $instance['chars']; ?>" style="width:20%;" />
		</p>
		
		<!-- Append post type to title -->
		<p>
			Show post type in title:  
			<select id="<?php echo $this->get_field_id( 'cf_title_type' ); ?>" name="<?php echo $this->get_field_name( 'cf_title_type' ); ?>" >
				<option value="yes"<?php echo ($instance['cf_title_type'] == 'yes') ? ' selected' : '' ;?>>Yes</option>				
				<option value="no"<?php echo ($instance['cf_title_type'] == 'no') ? ' selected' : '' ;?>>No</option>				
			</select>
		</p>
		
		<!-- Feeds to include from the Plaza -->
		<p>
			Feeds to Include:<br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'cf_topics' ); ?>" name="<?php echo $this->get_field_name( 'cf_topics' ); ?>" value="1"<?php echo ($instance['cf_topics'] == 1) ? ' checked' : ''; ?> /> Topics <br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'cf_events' ); ?>" name="<?php echo $this->get_field_name( 'cf_events' ); ?>" value="1"<?php echo ($instance['cf_events'] == 1) ? ' checked' : ''; ?> /> Events <br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'cf_prayers' ); ?>" name="<?php echo $this->get_field_name( 'cf_prayers' ); ?>" value="1"<?php echo ($instance['cf_prayers'] == 1) ? ' checked' : ''; ?> /> Prayers <br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'cf_needs' ); ?>" name="<?php echo $this->get_field_name( 'cf_needs' ); ?>" value="1"<?php echo ($instance['cf_needs'] == 1) ? ' checked' : ''; ?> /> Needs <br />
			<input type="checkbox" id="<?php echo $this->get_field_id( 'cf_albums' ); ?>" name="<?php echo $this->get_field_name( 'cf_albums' ); ?>" value="1"<?php echo ($instance['cf_albums'] == 1) ? ' checked' : ''; ?> /> Photos
		</p>
		
		<!-- Decide how to sort -->
		<p>
			<label for="<?php echo $this->get_field_id( 'sort' ); ?>">Sort Feeds:</label>
			<select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>" >
				<option value="creation_desc"<?php echo ($instance['sort'] == 'creation_desc') ? ' selected' : '' ;?>>Creation Date Decending</option>
				<option value="creation_asc"<?php echo ($instance['sort'] == 'creation_asc') ? ' selected' : '' ;?>>Creation Date Acending</option>				
			</select>			
		</p>

		<!-- Loading Icon to use for feed -->
		<p>
			Loading Icon to Use:<br />
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-default" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="default"<?php echo ($instance['icon'] == 'default') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader.gif" alt="Default" />&nbsp;<span style="font-size:80%">(default)</span><br />
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-black" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-black"<?php echo ($instance['icon'] == 'white-black') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-black.gif" alt="White with black arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-blue" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-blue"<?php echo ($instance['icon'] == 'white-blue') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-blue.gif" alt="White with blue arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-green" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-green"<?php echo ($instance['icon'] == 'white-green') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-green.gif" alt="White with green arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-orange" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-orange"<?php echo ($instance['icon'] == 'white-orange') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-orange.gif" alt="White with orange arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-purple" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-purple"<?php echo ($instance['icon'] == 'white-purple') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-purple.gif" alt="White with purple arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-white-red" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="white-red"<?php echo ($instance['icon'] == 'white-red') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-white-red.gif" alt="White with red arrows" /><br />
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-white" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-white"<?php echo ($instance['icon'] == 'black-white') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-white.gif" alt="Black with white arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-blue" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-blue"<?php echo ($instance['icon'] == 'black-blue') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-blue.gif" alt="Black with blue arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-green" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-green"<?php echo ($instance['icon'] == 'black-green') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-green.gif" alt="Black with green arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-orange" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-orange"<?php echo ($instance['icon'] == 'black-orange') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-orange.gif" alt="Black with orange arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-purple" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-purple"<?php echo ($instance['icon'] == 'black-purple') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-purple.gif" alt="Black with purple arrows" />&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'icon' ); ?>-black-red" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="black-red"<?php echo ($instance['icon'] == 'black-red') ? ' checked' : ''; ?> /><img src="<?=site_url();?>/wp-content/plugins/city-feeds-widget/ajax-loader-black-red.gif" alt="Black with red arrows" />&nbsp;&nbsp;
		</p>
<?php
	}
}

/**
 * Formats the dates for output
 *
 * Removes -00:00 which causes bugs with Strtotime
 */
 
function format_dates($out_format='M j, Y \a\t g:i a', $date='') 
{
	if ($date == '')
	{
		$date = date('Y-m-d H:i:s');
	}
			
	$output = preg_replace('/-[0-9]{2}:[0-9]{2}/', '', $date);
	
	return date($out_format, strtotime($output));
}

/**
 * Add function to widgets_init that'll load our widget.
 */
add_action('widgets_init', create_function('', 'return register_widget("CityfeedsWidget");'));

 /* End of File */