<?php use yii\helpers\Url; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
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
                                    <input id="chatText" type="text" name="chatText" placeholder="Message ..." class="form-control" maxlength="510">
                                    <span class="input-group-btn">
                                      <button type="submit" value="Submit" class="btn btn-primary btn-flat">
                                          Envoyer
                                          <span class="spinner fa fa-spinner fa-spin hidden"></span>
                                      </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>