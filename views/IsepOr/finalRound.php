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
        <h2 style="margin: 30px 0 10px 0;"><?php echo __('ISEPOR_EXTRA_SEPARATEUR'); ?></h2>
        <p style="width: 500px;  margin-bottom: 20px;">
            <?php echo __('ISEPOR_TEXT_EXTRA'); ?>
        </p>
    <?php foreach($questionsExtra as $questionExtra): ?>
        <div id="question-<?php echo $questionExtra['id'] ?>">
            <h2><label for="question-<?php echo $questionExtra['id'] ?>-input"><?php echo htmlspecialchars($questionExtra['questions']) ?> :</label></h2>
            <p itemid="<?php echo $questionExtra['id'] ?>">
                <input type="hidden" value="<?php echo htmlspecialchars($questionExtra['type']); ?>" id="question-<?php echo $questionExtra['id'] ?>-type" />
                <input type="hidden" value="extra" id="question-<?php echo $questionExtra['id'] ?>-has-extra" />
                <input type="hidden" value="<?php echo htmlspecialchars($questionExtra['extra']); ?>" id="question-<?php echo $questionExtra['id'] ?>-extra" />
                <input type="text" id="question-<?php echo $questionExtra['id']; ?>-input" class="autocomplete" style="margin: 5px;"/>
                <span id="question-<?php echo $questionExtra['id'] ?>-error-com" class="error hidden" ><?php echo __('ISEPOR_ERROR_AUTOCOMPLETE'); ?></span>
                <span id="question-<?php echo $questionExtra['id'] ?>-error-nan" class="error hidden" ><?php echo __('ISEPOR_ERROR_NOT_EXIST'); ?></span>
                <span id="question-<?php echo $questionExtra['id'] ?>-error-emp" class="error hidden" ><?php echo __('ISEPOR_ERROR_EMPTY'); ?></span>
                <input class="valid" type="hidden" name="valid-<?php echo htmlspecialchars($questionExtra['type']); ?>-<?php echo $questionExtra['id'] ?>-extra" id="question-<?php echo $questionExtra['id'] ?>-valid" value="" />
            </p>
        </div>
    <?php endforeach; ?>
        <div class="submit">
            <input type="submit" value="Envoyer !"/>
        </div>
    </form>
</div>
<?php else : ?>
<div style="text-align: center;">
    <h1><?php echo __('ISEPOR_VOTE_OK'); ?></h1>
    <img src="<?php echo Config::URL_STATIC; ?>images/others/ok.png" alt="" />
</div>
<?php endif; ?>
