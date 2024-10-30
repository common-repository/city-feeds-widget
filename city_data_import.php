<?php
/*
This is a helper file that gets the data for the AJAX call
Author: Eric Falkner
Version: 1.2.1
*/

	/**
	 * Formats the dates for output
	 *
	 * Removes -00:00 which causes bugs with Strtotime
	 */
	 
	if ( ! function_exists('format_dates'))
	{
		function format_dates($out_format='M j, Y \a\t g:i a', $date='') 
		{
			if ($date == '')
			{
				$date = date('Y-m-d H:i:s');
			}
					
			$output = preg_replace('/-[0-9]{2}:[0-9]{2}/', '', $date);
			
			return date($out_format, strtotime($output));
		}
	}


	/**
	 * How to display the widget on the screen.
	 */

	if (! $_POST)
	{
		echo 'No feeds found';
		exit();
	}
	
	//set the instance var to the POST	
	$instance = $_POST;

	//Process the url provided to get the JSON
	$data = parseCityJSON($instance);

	//check if the data is returned
	if ($data)
	{
		$loop_count = 1; //loop check for max count
		foreach ($data as $entry)
		{
			//if the loop count exceeds the limit set, break the loop
			if ($loop_count > $instance['limit'])
			{
				break;
			}
			else
			{
				//check post type:  global_topic, global_album, global_event, global_prayer, global_need
				if (isset($entry->global_topic))
				{
					$type = 'global_topic';
					$type_check = 'cf_topics';
				}
				elseif (isset($entry->global_album))
				{
					$type = 'global_album';
					$type_check = 'cf_albums';
				}
				elseif (isset($entry->global_event))
				{
					$type = 'global_event';
					$type_check = 'cf_events';
				}
				elseif (isset($entry->global_prayer))
				{
					$type = 'global_prayer';
					$type_check = 'cf_prayers';
				}
				elseif (isset($entry->global_need))
				{
					$type = 'global_need';
					$type_check = 'cf_needs';
				}

				//check if we want the type shown
				if ($instance[$type_check] == 1) 
				{
					//check the creation date limit
					$too_old = TRUE;
					if ($instance['age'] == 'all')
					{
						$too_old = FALSE;
					}
					else
					{
						if (strtotime($instance['age'], strtotime($entry->$type->created_at)) > strtotime('now'))
						{
							$too_old = FALSE;
						}
					}

					//if the post is not too old, process it and show it
					if ($too_old === FALSE)
					{
						$max_chars = ($instance['chars'] AND is_numeric($instance['chars'])) ? (int)$instance['chars'] : -1;
						$body_content = (strlen(strip_tags($entry->$type->body)) > $max_chars AND $max_chars != -1) ? substr(strip_tags($entry->$type->body), 0, $max_chars) . '...' : strip_tags($entry->$type->body);
						
						echo '<ul class="thumbs no_arrow"><li>';

						//modify the headers for item type
						$title_print = ucfirst(str_replace('global_', '', $entry->$type->title));
						if ($instance['cf_title_type'] == 'yes') 
						{
							$title_print = ucfirst(str_replace('global_', '', $type)) . ': ' . $title_print;
						}
						
						//output the title
						//we need to add a block display to force formatting for certain templates
						echo '<a style="display: block;" class="title" href="' . $entry->$type->short_url . '" title="' . $title_print . '" target="_blank">' . $title_print . '</a>';
						
						//if we have an event, output the start and end date/times
						if ($type == 'global_event')
						{
							//2011-04-24T09:00:00-07:00
							echo 'Starts: ' . format_dates('M j, Y \a\t g:i a', $entry->$type->starting_at) . '<br />';
							echo 'Ends: '   . format_dates('M j, Y \a\t g:i a', $entry->$type->ending_at);
						}
						
						echo '<span style="display: block;" class="comments_body">' . $body_content . '</span>';
						
						if ($type == 'global_album')
						{
							//if there is an album, calculate the total photos, and grab a cross-section of the photos
							if (isset($entry->$type->photos) AND is_array($entry->$type->photos))
							{
								$total_photos = count($entry->$type->photos);
								$first_photo = 0;
								$second_photo = floor($total_photos/3);
								$third_photo = floor($total_photos/3) * 2;
							}
							
							//output the thumbnails in the feed
							if (isset($entry->$type->photos[$first_photo]))
							{
								echo '<br />';
								echo '<br />';
								echo '<a href="' . $entry->$type->short_url . '" title="' . $entry->$type->photos[$first_photo]->photo->caption . '" target="_blank">';
								echo '<img style="max-width: 72px; max-height: 72px; display: inline; padding: 1px; border: 1px solid rgb(204, 204, 204);" src="'.$entry->$type->photos[$first_photo]->photo->normal_image.'" alt="'.$entry->$type->photos[$first_photo]->photo->caption.'" title="'.$entry->$type->photos[$first_photo]->photo->caption.'"  />';
								echo '</a>';
							}
							if (isset($entry->$type->photos[$second_photo]))
							{
								echo '<a href="' . $entry->$type->short_url . '" title="' . $entry->$type->photos[$second_photo]->photo->caption . '" target="_blank">';
								echo '<img style="max-width: 72px; max-height: 72px; display: inline; margin-left:10px; padding: 1px; border: 1px solid rgb(204, 204, 204);" src="'.$entry->$type->photos[$second_photo]->photo->normal_image.'" alt="'.$entry->$type->photos[$second_photo]->photo->caption.'" title="'.$entry->$type->photos[$second_photo]->photo->caption.'" />';
								echo '</a>';
							}
							if (isset($entry->$type->photos[$third_photo]))
							{
								echo '<a href="' . $entry->$type->short_url . '" title="' . $entry->$type->photos[$third_photo]->photo->caption . '" target="_blank">';
								echo '<img style="max-width: 72px; max-height: 72px; display: inline; margin-left:10px; padding: 1px; border: 1px solid rgb(204, 204, 204);" src="'.$entry->$type->photos[$third_photo]->photo->normal_image.'" alt="'.$entry->$type->photos[$third_photo]->photo->caption.'" title="'.$entry->$type->photos[$third_photo]->photo->caption.'"  />';
								echo '</a>';
							}
						}
						
						//if we have a non-event post, output the creation date
						if ($type != 'global_event')
						{
							echo '<span style="display: block; padding: 5px 0px;" class="comments_date">' . format_dates($instance['date_format'], $entry->$type->created_at) . '</span>';
						}
						echo '</li></ul>';
						
					}
					
					$loop_count++;
				}

			}
		}
	}
	else
	{
		echo 'No feeds found';
	}

/**
 * Gets the JSON Data
 */
	function parseCityJSON($instance = '') 
	{
		//extract the instance into variables
		extract($instance, EXTR_OVERWRITE);
		
		//if the url doesn't have http, add it and then append the format
		if (stripos($url, 'http://') !== FALSE || stripos($url, 'https://') !== FALSE)
		{
			$url = str_replace('http://', '', $url);
			$url = str_replace('https://', '', $url);
		}
		if (stripos($url, 'onthecity.org') !== FALSE)
		{
			$url = str_replace('.onthecity.org', '', $url);
		}
		
		//@TODO make preg match to remove http, onthecity and any / url elements

		//build URLs
		$url_topics  = 'http://' . $url . '.onthecity.org/plaza/topics?format=json';
		$url_events  = 'http://' . $url . '.onthecity.org/plaza/events?format=json';
		$url_prayers = 'http://' . $url . '.onthecity.org/plaza/prayers?format=json';
		$url_needs   = 'http://' . $url . '.onthecity.org/plaza/needs?format=json';
		$url_albums  = 'http://' . $url . '.onthecity.org/plaza/albums?format=json';
	
		//grab the city information in JSON Format
		$data_topics  = ($cf_topics  == 1) ? @file_get_contents($url_topics) : '[]';
		$data_events  = ($cf_events  == 1) ? @file_get_contents($url_events) : '[]';
		$data_prayers = ($cf_prayers == 1) ? @file_get_contents($url_prayers) : '[]';
		$data_needs   = ($cf_needs   == 1) ? @file_get_contents($url_needs) : '[]';
		$data_albums  = ($cf_albums  == 1) ? @file_get_contents($url_albums) : '[]';

		//Hack to remove bug with invalid JSON returned by the City sometimes
		$data_topics  = str_replace('},}]', '}}]', $data_topics);
		$data_events  = str_replace('},}]', '}}]', $data_events);
		$data_prayers = str_replace('},}]', '}}]', $data_prayers);
		$data_needs   = str_replace('},}]', '}}]', $data_needs);
		$data_albums  = str_replace('},}]', '}}]', $data_albums);

		//decode the feeds
		$data['topics']  = json_decode($data_topics);
		$data['events']  = json_decode($data_events);
		$data['prayers'] = json_decode($data_prayers);
		$data['needs']   = json_decode($data_needs);
		$data['albums']  = json_decode($data_albums);

		//re-combine all items and re-sort array by date created
		$feeds = array('topics', 'events', 'prayers', 'needs', 'albums');
		$new_data = array();
		
		foreach ($feeds as $feed)
		{
			$event_type = 'global_' . str_replace('s', '', $feed);
			
			if (! empty($data[$feed]))
			{
				foreach ($data[$feed] as $sub_data)
				{
					$key = ($feed == 'events') ? strtotime($sub_data->$event_type->ending_at) : strtotime($sub_data->$event_type->created_at);
					$new_data[$key] = $sub_data;
				}
			}
		}

		//resort the feeds by date (key)
		//sort by creation date ascending (old to new)
		if ($sort == 'creation_asc')
		{
			ksort($new_data);
		}
		//sort by creation date decending (new to old - default)
		else
		{	
			krsort($new_data);
		}
		
		return $new_data;
	}
	
 /* End of File */