<div id="wiki-nav-bar">
    <a href="/kb/" class="wiki-nav-links">Knowledge Base Home</a>
    <?php 
    //Find out the ID of the articles page and check if the user has access to create brand new wiki posts
    global $wpdb;
    $values = array();
    
    $sql = "SELECT ID
            FROM wp_posts
            WHERE post_name = 'articles' AND post_type = 'incsub_wiki'";
    $result = $wpdb->get_results($sql, ARRAY_A);
    if($result[0]['ID'] != NULL) {
        if (current_user_can('edit_wiki', $result[0]['ID'])) { ?>
        <a href="/kb/articles/?action=edit&eaction=create" class="wiki-nav-links">Create New Knowledge Base Article</a>
    <?php 
        } 
     }?>
    
</div>