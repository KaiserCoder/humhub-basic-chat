<?php
use yii\helpers\Url;
?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>PonyChat</strong> Chat
        </div>
        <div class="panel-body">
            <div id="chatContainer">
                <div id="chatLineHolder"></div>
                <div id="chatUsers" class="rounded"></div>
                <div id="chatBottomBar" class="rounded">
                    <div class="tip"></div>
                    <form id="submitForm" action="<?= Url::toRoute('/ponychat/chat/submit') ?>" method="post">
                        <div class="input-group">
                            <input id="chatText" type="text" class="form-control" name="chatText" placeholder="Message ..." autocomplete="off"/>
                            <span class="input-group-btn">
                                <button type="submit" value="Submit" class="btn btn-primary btn-flat">Envoyer <span class="spinner fa fa-spinner fa-spin hidden"></span></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default markup">
        <button id="uButton" type="button" class="btn btn-default" style="text-decoration:underline">Souligné</button>
        <button id="iButton" type="button" class="btn btn-default" style="font-style:italic">Italique</button>
        <button id="bButton" type="button" class="btn btn-default" style="font-weight:bold">Gras</button>
        <button id="rainbowButton" type="button" class="btn btn-rainbow">Rainbooow</button>
        <button id="spoilerButton" type="button" class="btn btn-default"><i class="fa fa-angle-right"></i> Spoiler</button>
        <button id="videoButton" type="button" class="btn btn-default"><i class="fa fa-arrow-down"></i> Video</button>
        <button id="urlButton" type="button" class="btn btn-default"><i class="fa fa-arrow-down"></i> Url</button>
        <button id="imgButton" type="button" class="btn btn-default"><i class="fa fa-arrow-down"></i> Image</button>
        <div id="buttonInput"></div>
    </div>
    <div class="panel panel-default smileys">
    <?php foreach ($smileys as $smiley): ?>
        <a href="javascript:smiley(':<?= str_replace('.png', ':', $smiley) ?>')">
            <img src="<?= Yii::getAlias('@web') ?>/img/smiley/<?= $smiley ?>" alt="<?= $smiley ?>" class="smiley"/>
        </a>
    <?php endforeach ?>
    </div>
</div>