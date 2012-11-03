<?php if($empty_post): ?>
<div id="isepor">
    <h1><?php echo __('ISEPOR_TITLE'); ?></h1>
    <p style="width: 500px;  margin-bottom: 20px;">
        <?php echo __('ISEPOR_TEXT'); ?>
    </p>
    <form method="post" action="<?php echo Config::URL_ROOT.Routes::getPage('isep_or_2'); ?>" id="form-isepor-final" >
    <?php foreach($questions as $question): ?>
        <div id="question-<?php echo $question['id'] ?>">
            <h2><?php echo htmlspecialchars($question['questions']) ?> :</h2>
            <p style="margin: 5px;" itemid="<?php echo $question['id'] ?>"  class="valid">
            <?php foreach($datas[$question['id']] as $key => $data): ?>
                <input type="radio" name="valid-<?php echo htmlspecialchars($data['tableName']); ?>-<?php echo $question['id'] ?>" value="<?php echo $data['valid']; ?>" id="radio-<?php echo $question['id'] ?>-<?php echo $key; ?>" />
                <label for="radio-<?php echo $question['id'] ?>-<?php echo $key; ?>"><?php echo $data['name'] ?></label>
                <br />
            <?php endforeach; ?>
                <span id="question-<?php echo $question['id'] ?>-error-emp" class="emptyError hidden"><?php echo __('ISEPOR_ERROR_EMPTY'); ?></span>
            </p>
        </div>
    <?php endforeach; ?>
    </form>
</div>
<?php else : ?>
<div style="text-align: center;">
    <h1><?php echo __('ISEPOR_VOTE_OK'); ?></h1>
    <img src="<?php echo Config::URL_STATIC; ?>images/others/ok.png" alt="" />
</div>
<?php endif; ?>
