<div id="wiki-nav-bar">
    <?php 
    echo '<a href="/kb/"><img src="/wp-content/images/kb/kb';
    $ranNum = rand(1,4); 
    echo $ranNum;
    echo '.png" class="wiki-header-img"/></a>';
    
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
    }
    $_SESSION['wiki'] = 1; //Setting the flag to use a wiki search only 
    ?>
    <hr>
</div>