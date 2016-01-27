<div id="wiki-nav-bar">
    <a href="/kb/" class="wiki-nav-links">Knowledge Base Home</a>
    <?php if (current_user_can('edit_wiki')) { ?>
    <a href="/kb/articles/?action=edit&eaction=create" class="wiki-nav-links">Create New Knowledge Base Article</a>
    <?php } ?>
</div>