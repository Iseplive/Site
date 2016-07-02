<div id="isepor">
    <h1><?php echo __('ISEPOR_TITLE_RESULTS'); ?></h1>
    <?php foreach($questions as $question): ?>
        <div id="question-<?php echo $question['id'] ?>" style="line-height: 1.6em;">
            <h2><?php echo htmlspecialchars($question['questions']) ?> :</h2>
            <?php foreach($datas[$question['id']] as $key => $data): ?>
                <?php $pourcent = ($countUser[$question['id']] > 0) ? ((((int) $data['cmpt'])*100)/((int) $countUser[$question['id']])) : 0; ?>
                <div class="progressBarOutter">
                    <div style="float:left;"><?php echo __('ISEPOR_VOTES_RESULTS', array('perc' => round($pourcent, 1), 'votes' => (int) $data['cmpt'])); ?></div>
                    <div class="progressBarInner" style="width: <?php echo round($pourcent*2.44); ?>px"></div>
                </div>
                <div style="padding: 6px; float:left; width: 400px; margin-left: 10px; margin: 5px;">
                    <?php if($key == 0) : ?>
                        <b><?php echo $data['name'] ?></b>
                    <?php else : ?>
                        <?php echo $data['name'] ?>
                    <?php endif; ?>
                </div>
            <div class="clear">&nbsp;</div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
