<?php

/*
*Template Name: IT Survey Admin
*
*todo description
*
*
*/
?>
<?php get_header(); ?>
<style type='text/css'>

div.search {
		display:none;
    }
</style>
<div id="content">
	<div id="main-content">
		<h1 class="replace" style="float:left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		<?php
			function isAdmin(){
				$current_user = wp_get_current_user();
				foreach($current_user->roles as $role){
					if($role == 'administrator'){
						return true;
					}
				}
				return false;
			}
			
			if (isAdmin()){ 
				global $wpdb;
				$sql = "SELECT * FROM `it_survey` ORDER BY  `it_survey`.`time` DESC";
				$results = $wpdb->get_results($sql);
				$headers = $array_key = array_keys(get_object_vars($results[0]));
				?>
				<table>
					<tr>
						<?php foreach($headers as $head){
							echo "<th>".$head."</th>\n";
						} ?>
					</tr>
						<?php foreach($results as $result){
							echo "<tr>";
							$result = get_object_vars($result);
							foreach($headers as $head){
								echo "<td>";
								switch($head) {
									case 'ticket_id':
										if ($result['ticket_id'] == 0){
											echo 'invalid ticket';
										}
										else{
											echo "<a href='http://helpdesk:9876/tickets/list/single_ticket/".$result['ticket_id']."' target='_blank'>Ticket: ".$result['ticket_id']."</a>";
										}
										break;
									case 'how_well':
										if ($result['how_well'] == 0){
											echo 'NULL';
										}
										else {
											echo $result['how_well'];
										}
										break;
									default:
										echo $result[$head];
								}
								echo "</td>\n";
							}
							echo "</tr>";
						}
						?>
				</table>
				<?php }
			else { ?>
				<BR><BR><div>This is an administrative page.  To view page you must login as an administrator.</div>
			<?php }
			
		?>
    </div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>