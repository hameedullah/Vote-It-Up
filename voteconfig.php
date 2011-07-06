<?php
/* VoteItUp configuration page */

function VoteItUp_options() {
	if (function_exists('add_options_page')) {
	add_options_page("Vote It Up", "Vote It Up", 8, "voteitupconfig", "VoteItUp_optionspage");
	add_options_page("Edit Votes", "Edit Votes", 8, "voteitupeditvotes", "VoteItUp_editvotespage");
	}
}

/* Wordpress MU fix, options whitelist */
if(function_exists('wpmu_create_blog')) {
add_filter('whitelist_options','voteitup_alter_whitelist_options');
function voteitup_alter_whitelist_options($whitelist) {
if(is_array($whitelist)) {
$option_array = array('voteitup' => array('voteiu_initialoffset','voteiu_votetext','voteiu_sinktext','voteiu_aftervotetext','voteiu_allowguests','voteiu_allowownvote','voteiu_limit','voteiu_widgetcount','voteiu_skin'));
$whitelist = array_merge($whitelist,$option_array);
}
return $whitelist;
}

}

//Page meant for administrators
function VoteItUp_optionspage() {

?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php _e('Voting options'); ?></h2>
<form method="post" action="options.php">
<?php
/* bugfix for wordpress mu */
if(function_exists('wpmu_create_blog')) {
wp_nonce_field('voteitup-options');
echo '<input type="hidden" name="option_page" value="voteitup" />';
} else {
wp_nonce_field('update-options');
}
?>

<h3>General</h3>
<table class="form-table" border="0">
<tr valign="top">
<th scope="row" style="text-align: left;">Initial vote count</th>
<td>
<input type="text" name="voteiu_initialoffset" id="voteiu_initialoffset" value="<?php if (get_option('voteiu_initialoffset')=='') { echo '0'; } else { echo get_option('voteiu_initialoffset'); } ?>" />
</td></tr>
<tr valign="top">
<th scope="row" style="text-align: left;">Name of positive votes</th>
<td>
<input type="text" name="voteiu_votetext" id="voteiu_votetext" value="<?php echo htmlentities(get_option('voteiu_votetext')); ?>" /><br />
You can use <code>&lt;img&gt;</code> to use images instead of text. Example: <code>&lt;img src=&quot;<?php echo VoteItUp_ExtPath(); ?>/uparrow.png&quot; /&gt;</code><br />
Default: <code>Vote</code>
</td>
</tr>
<tr valign="top">
<th scope="row" style="text-align: left;">Name of negative votes</th>
<td>
<input type="text" name="voteiu_sinktext" id="voteiu_sinktext" value="<?php echo htmlentities(get_option('voteiu_sinktext')); ?>" <?php if (!GetCurrentSkinInfo('supporttwoway')) { echo 'disabled="disabled" '; }?>/><br />
<?php if (GetCurrentSkinInfo('supporttwoway')) { ?>You can use <code>&lt;img&gt;</code> to use images instead of text. Example: <code>&lt;img src=&quot;<?php echo VoteItUp_ExtPath(); ?>/downarrow.png&quot; /&gt;</code><br />
If this is left blank two-way voting is disabled.<?php } else {
?>Current widget template does not support two-way voting<?php } ?>
</td>
</tr>
<tr valign="top">
<th scope="row" style="text-align: left;">Text displayed after vote is cast</th>
<td>
<input type="text" name="voteiu_aftervotetext" id="voteiu_aftervotetext" value="<?php echo htmlentities(get_option('voteiu_aftervotetext')); ?>" /><br />
You can use <code>&lt;img&gt;</code> to use images instead of text. Text is displayed after user casts a vote. If this is left blank the vote button disappears.
</td>
</tr>
</table>

<h3>Permissions</h3>
<table class="form-table" border="0">
<tr valign="top">
<th scope="row" style="text-align: left;">Allow guests to vote</th>
<td>
<input type="checkbox" name="voteiu_allowguests" id="voteiu_allowguests" value="true" <?php if (get_option('voteiu_allowguests') == 'true') { echo ' checked="checked"'; } ?> />
</td></tr>
<tr valign="top">
<th scope="row" style="text-align: left;">Post author can vote own post</th>
<td>
<input type="checkbox" name="voteiu_allowownvote" id="voteiu_allowownvote" value="true" <?php if (get_option('voteiu_allowownvote') == 'true') { echo ' checked="checked"'; } ?> />
</td></tr>
</table>

<h3>Theming</h3>
<p>External templates for the voting widgets can be installed via the &quot;skin&quot; directory. Voting widgets using <code>&lt;?php DisplayVotes(get_the_ID()); ?&gt;</code> will use the new themes. Setting this to &quot;none&quot; will result in the default bar theme being used.</p>
<?php SkinsConfig(); ?>

<h3>Widget</h3>
<p>The widget shows posts which have the most votes. Only new posts are considered to keep the list fresh.</p>
<p>The widget can be displayed to where you want by using the following code: <code>&lt;?php MostVotedAllTime(); ?&gt;</code>, or if your template supports widgets it can be added via the <a href="widgets.php" title="Widgets">widgets panel</a>.</p>
<table class="form-table" border="0">
<tr valign="top">
<th scope="row" style="text-align: left;">No. of most recent posts to be considered</th>
<td><input type="text" name="voteiu_limit" id="voteiu_limit" value="<?php echo get_option('voteiu_limit'); ?>" /><br />
Default: <code>100</code>
</td>
</tr>
<tr valign="top">
<th scope="row" style="text-align: left;">No. of posts shown in widget</th>
<td><input type="text" name="voteiu_widgetcount" id="voteiu_widgetcount" value="<?php if (get_option('voteiu_widgetcount')=='') { echo '10'; } else {echo get_option('voteiu_widgetcount');} ?>" /><br />
Default: <code>10</code>
</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="voteiu_initialoffset,voteiu_votetext,voteiu_sinktext,voteiu_aftervotetext,voteiu_allowguests,voteiu_allowownvote,voteiu_limit,voteiu_widgetcount,voteiu_skin" />

<h3>Voting code</h3>
<p>The following code should be added in your index.php and single.php. This displays the vote buttons.</p>
<p><strong>Themable Version</strong><br />
<code>&lt;?php DisplayVotes(get_the_ID()); ?&gt;</code></p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
</p>
</form>


</div>
<?php

}

/* VoteItUp edit votes page */

function VoteItUp_editvotespage() {
VoteBulkEdit();
?>
<div class="wrap">
<div id="icon-edit" class="icon32"><br /></div>
<h2><?php _e('Edit Votes'); ?></h2>
<form method="post" action="">
<?php /* wp_nonce_field('update-options'); */ ?>
<div class="tablenav">

<div class="alignleft actions">

<select name="action1">
<option value="-1" selected="selected">Bulk Actions</option>
<option value="delete">Reset Votes</option>
<option value="deleteuser">Reset User Votes</option>
<option value="deleteguest">Reset Guest Votes</option>
</select>
<input type="submit" value="Apply" name="doaction1" id="doaction1" class="button-secondary action" />
</div></div>

<?php DisplayPostList(); ?>

<div class="tablenav">


<div class="alignleft actions">
<select name="action2">
<option value="-1" selected="selected">Bulk Actions</option>
<option value="delete">Reset Votes</option>
<option value="deleteuser">Reset User Votes</option>
<option value="deleteguest">Reset Guest Votes</option>
</select>

<input type="submit" value="Apply" name="doaction2" id="doaction2" class="button-secondary action" />
<br class="clear" />
</div>
<br class="clear" />
</div>

</form>

</div>
<?php
}


?>