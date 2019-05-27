
 //Site Options	
function custom_sites() {

	$mysites = get_sites(); 
	if ( ! empty ( $mysites ) ) {
 
 		/* Define repeat variables and arrays: URL, table prefix, site ID  */
		Global $wpdb;
		$current_blog = $wpdb->blogid;
		$thead = 	'<th id="header row" >Network-wide Site Options</th>';
		
		//SQL fields for site option values distinct option name with grooup prefix
		$optlist = 
		"select 
			trim('_' from option_name) as opt,
			option_name as name,
			option_value as setting,
			(Case 
				when Locate('_',trim('_' from option_name)) > 0
				then LEFT(trim('_' from option_name),Locate('_', trim('_' from option_name))-1)
				when Locate('-',trim('_' from option_name)) > 0
				then LEFT(trim('_' from option_name),Locate('-', trim('_' from option_name))-1)
				else trim('_' from option_name)
			end) as optgroup from ";

			//exclude transients: where not-like statement
			$vara = $wpdb->esc_like( "_transient_" ) . "%";
			$varb = $wpdb->esc_like( "_site_transient_" ) . "%";
			$varc = $wpdb->esc_like( "displayed_galleries_" ) . "%";

			$where = " WHERE NOT (option_name like %s or option_name like %s or option_name like %s) ";	

	
	/* Get options list for each site */		
		foreach( $mysites as $mysite ) {
			  
			/* Store repeat variables: URL, table prefix, site ID  */												
			$mysite_id = get_object_vars( $mysite )['blog_id'];
			switch_to_blog($mysite_id);
			$domain = get_object_vars( $mysite )['domain'];
			$prefix = {$wpdb->prefix};

			$thead .= '<th id="' . $prefix . '" >' . $domain . '</th>';

			/* build sql to pull Option List from options table */
				$optlistsql = $optlist . $wpdb->options . $where;				//sql prepare insert wild cards
				/* sql prepare - insert wild cards tp %type */	
				$optlistsql = $wpdb->prepare( $optlistsql, $vara, $varb, $varc );
				$getoptions = $wpdb->get_results($optlistsql);
				$network_options = $network_options + $getoptions;     
		}
		//Return to current site with data
		switch_to_blog($current_blog);	
		
          //build table header			
 		
		echo '<div style="overflow-x:auto;">';
		echo '<table id="options" border="2px solid black" cellspacing="0" cellpadding="0" width="100%" >';
		echo '<thead>' . $thead . '</thead>';	
		
		$sortoptgroup = array_column( $network_options,'optgroup' );
		foreach( $sortoptgroup as $optgroup ) {
			echo '<tbody id ="' . $optgroup . '" >';
				
				$listoptgroup = filter_by_value ($network_options, 'optgroup', $optgroup);
				$sortopt = array_column( $listoptgroup,'opt' );
				
				foreach( $sortopt as $opt ) {
					echo '<tr><th>' . $opt . '</th>';
						
						$listopt =  filter_by_value ($listoptgroup, 'opt', $opt);
						// $sortoptname = array_column( $listopt,'optname' );
						
						foreach( $mysites as $mysite ) {
							echo '<td>';
								foreach( $listopt as $list ) {
									if( $list->siteid == $mysite ) {
										4$listoptsite[] = $list->optval;
									}
								} 
							$optval = implode( '<br />* ',esc_html($listoptsite ))
							echo $optval . '</td>';
						}
					echo '</tr>';
				}
			echo '</tbody>';
		}
		echo '</tbody></table></div>'; 	
	}
}
add_action( 'wpmu_options', 'custom_sites' );
    /** END /Site Options */
    
