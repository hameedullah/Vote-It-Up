<?php
/*
Vote It Up Version 1.0.7 and above
This file is used for skinning purposes.
To install the voting widget with the updated skin, you'll need to use DisplayVotes(get_the_ID())

Forceful usage of themes: DisplayVotes(get_the_ID(), [name of theme]);

Pre-installed themes:
-Ticker
-Snap (Ticker)
-Bar
*/

function GetPathInfo($path, $level = 0) {
$r = array();
if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
	if ($file != '.' && $file != '..') {
        $r[count($r)] = $file;
	}
    }
}

return $r;
}

function AvailableSkins() {
$skins = '';
$filepath = str_replace("\\", "/", dirname(__FILE__))."/skins";

$skins = GetPathInfo($filepath);
return $skins;
}

function IsChecked($name) {
if (get_option('voteiu_skin') == $name) {
echo 'checked';
}
if ($name == '') {
if (get_option('voteiu_skin') == 'none' | get_option('voteiu_skin') == '') {
echo 'checked';
}
}
}

function SkinsConfig() {
$skins = AvailableSkins();
if (count($skins) > 0) {
?>
<div style="clear:both">
<div style="float:left; padding: 5px;">
<input type="radio" name="voteiu_skin" id="voteiu_skin" value="" <?php IsChecked(''); ?> /> Default<br />
</div>
<?php
for ($i = 0; $i < count($skins); $i++) {
?>
<div style="float:left; padding: 5px"><input type="radio" name="voteiu_skin" id="voteiu_skin" value="<?php echo $skins[$i]; ?>" <?php IsChecked($skins[$i]); ?> /> <?php echo ucfirst(str_replace('_', ' ', $skins[$i])); ?></div>
<?php
}
}
?>
</div><br />
<?php
}

function LoadSkinHeader($skin) {
if ($skin != '') {
?>
<link rel="stylesheet" href="<?php echo VoteItUp_ExtPath(); ?>/skins/<?php echo $skin; ?>/votestyles.css" type="text/css" />
<script type="text/javascript" src="<?php echo VoteItUp_ExtPath(); ?>/skins/<?php echo $skin; ?>/voterajax.js"></script>
<?php
}


}

function LoadSkin($skin) {
@include_once(VoteItUp_Path()."/skins/".$skin."/skin.php");

if (function_exists('LoadVote')) {
LoadVote();
return true;
} else {
return false;
}
}

function LoadSkinWidget($skin, $mode) {
@include_once(VoteItUp_Path()."/skins/".$skin."/skin.php");

if (function_exists('LoadVoteWidget')) {
if ($mode == 'sidebar') {
if (function_exists('LoadVoteSidebarWidget')) {
LoadVoteSidebarWidget();
} else {
LoadVoteWidget();
}
} else {
LoadVoteWidget();
}
return true;
} else {
return false;
}
}

function LoadSkinVar($skin) {
//@include_once(str_replace("\\", "/", dirname(__FILE__))."/skins/".$skin."/skin.php");
@include_once(VoteItUp_Path()."/skins/".$skin."/skin.php");

if (function_exists('LoadVote')) {
return true;
} else {
return false;
}
}

function GetCurrentSkinInfo($var) {
@include_once(VoteItUp_Path()."/skins/".get_option('voteiu_skin')."/skin.php");
//Default skininfo
if (!function_exists('SkinInfo')) {

switch ($var) {
case 'name':
return 'Default';
break;
case 'supporttwoway':
return true;
break;
default:
return false;
break;
}

} else {
return SkinInfo($var);
}

}

?>