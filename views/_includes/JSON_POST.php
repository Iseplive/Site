
{"id":"<?php echo $post['id']; ?>",

<?php
if (isset($post['group_id']) && $post['official'] == '1') {
    $post_user_url = Config::URL_ROOT . Routes::getPage('group', array('group' => $post['group_url']));
    $post_user_name = $post['group_name'];
} else {
    $post_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $post['username']));
    $post_user_name = isset($post['firstname']) ? $post['firstname'] . ' ' . $post['lastname'] : $post['username'];
}

if (isset($post['avatar_url'])) {
    ?>
    "avatar":"<?php echo $post['avatar_url']; ?>",
<?php
} else {
    ?>
    "avatar":"",
<?php
}
?>

"userUrl":"<?php echo $post_user_url; ?>","username":"<?php echo htmlspecialchars($post_user_name); ?>",
"message":"<?php echo Text::inHTML($post['message']); ?>",

<?php
// Event
if (isset($post['event'])) {
    ?>
    "eventTitle":"<?php echo htmlspecialchars($post['event']['title']); ?>",
    "eventDate":"<?php echo Date::event(strtotime($post['event']['date_start']), strtotime($post['event']['date_end'])); ?>",
<?php
}



// Survey
if (isset($post['survey'])) {
    ?>
    "question":"<?php echo htmlspecialchars($post['survey']['question']); ?>",
    "answers":[
    <?php
    $ended = strtotime($post['survey']['date_end']) < time();
    $total_votes = 0;
    $voting = array();
    foreach ($post['survey']['answers'] as &$answer) {
        $total_votes += (int) $answer['nb_votes'];
        $answer['votes'] = $answer['votes'] == '' ? array() : json_decode($answer['votes'], true);
        $voting = array_unique(array_merge($voting, $answer['votes']));
    }
    $nb_voting = count($voting);

    $lm = 0;
    foreach ($post['survey']['answers'] as &$answer) {
        $lm++;
        // Results
        if ($post['survey']['multiple'] == '1')
            $perc = $nb_voting == 0 ? 0 : ((int) $answer['nb_votes']) / $nb_voting;
        else
            $perc = $total_votes == 0 ? 0 : ((int) $answer['nb_votes']) / $total_votes;
        $perc_s = round(100 * $perc);
        /*
         * 	Graph of colors
         * 	|\  /\  /    <-- blue, then red
         * 	| \/  \/
         * 	| /\  /\
         * 	|/  \/  \    <-- green
         * 	------------
         */
        if ($perc < 0.5) {
            $red = '00';
            $green = str_pad(dechex(255 * $perc * 2), 2, '0', STR_PAD_LEFT);
            $blue = str_pad(dechex(255 * (1 - $perc * 2)), 2, '0', STR_PAD_LEFT);
        } else {
            $red = str_pad(dechex(255 * ($perc - 0.5) * 2), 2, '0', STR_PAD_LEFT);
            $green = str_pad(dechex(255 * (1 - ($perc - 0.5) * 2)), 2, '0', STR_PAD_LEFT);
            $blue = '00';
        }
        ?>
        {"answer":"<?php echo htmlspecialchars($answer['answer']); ?>",
        "percent":"<?php echo $perc_s; ?>",
        "votes":"<?php echo __('POST_SURVEY_NB_VOTES', array('votes' => $answer['nb_votes'])); ?>"}
        <?php if (count($post['survey']['answers'])!=$lm) {?>,<?php}?>
    <?php
    }
    ?>
    ],

<?php
}
// Attachments
if (!isset($post['attachments']))
    $post['attachments'] = array();
$nb_photos = 0;
?>
"attachments":[
<?php
foreach ($post['attachments'] as $attachment) {
    switch ($attachment['ext']) {
        // Photo
        // see: http://flash-mp3-player.net/players/maxi/
        case 'jpg':
        case 'gif':
        case 'png':
            ?>
            {"image":"<?php echo $attachment['thumb']; ?>"}<?php if ($nb_photos + 1 != $post['attachments_nb_photos']) { ?>,<?php } ?>
            <?php
            $nb_photos++;
            break;
        case 'flv':
            ?>
            {"video":"<?php echo urlencode($attachment['url']); ?>"}
            <?php
            break;

        case 'mp4':
            ?>

            {"video":"<?php echo $attachment['url']; ?>"}

            <?php
            break;


// Audio
        case 'mp3':
            ?>
            {"mp3":"<?php echo urlencode($attachment['url']); ?>"}
            <?php
            break;


// Document
        default:
            ?>{"documents":"<?php echo $attachment['url']; ?>"}
            <?php
    }
}
?>
],
"comments":[
<?php
if (!isset($post['comments']) || !$is_logged)
    $post['comments'] = array();
$nb_comments = count($post['comments']);
$n = 0;
$comment_hidden = false;
$comments_at_the_beginning = floor(Config::COMMENTS_PER_POST / 2);
$comments_at_the_end = Config::COMMENTS_PER_POST - $comments_at_the_beginning;
foreach ($post['comments'] as $comment) {
    $n++;
    /* Cas ou il y a Trop de comment. */
    if ($nb_comments > Config::COMMENTS_PER_POST && !isset($one_post)) {
        if ($n == $comments_at_the_beginning + 1) {
            $comment_hidden = true;
            ?>
            {"postid":"<?php echo $post['id']; ?>",

        <?php
        } else if ($n == $nb_comments - $comments_at_the_end + 1) {
            $comment_hidden = false;
        }
    }
    ?>
    "commentid":"<?php echo $comment['id']; ?>",
    "username":"<?php echo $comment['username']; ?>",
    "avatar":"<?php echo $comment['avatar_url']; ?>",
    "realname":"<?php echo htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']); ?>",
    "message":"<?php echo Text::inHTML($comment['message']); ?>"}
    <?php if ($n!=$nb_comments) {?>,<?php} ?>
<?php }
debug_backtrace();
?>
]}