<?php
use yii\helpers\Url;
?>

<style>
    html {
        overflow: hidden !important;
    }
</style>

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
                            <input id="chatText" type="text" name="chatText" placeholder="Message ..." data-multiple/>
                            <span class="input-group-btn">
                                <button id="chatSubmit" type="submit" value="Submit" class="btn btn-primary btn-flat">Envoyer <span class="spinner fa fa-spinner fa-spin hidden"></span></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function() {

        var previous = 0;

        var awesomeplete = new Awesomplete(document.getElementById('chatText'), {
            minChars: 1,

            list: <?= $list ?>,

            filter: function(text, input) {
                return Awesomplete.FILTER_CONTAINS(text, input.match(/[^\s]*$/)[0]);
            },

            replace: function(text) {
                var before = this.input.value.match(/^.+\s|/)[0];
                this.input.value = before + text;
                this.minChars = this.input.value.length + 2;
            }
        });

        $('#chatText').on('change keyup copy paste cut', function() {
            if ($(this).val().length < previous)
                awesomeplete.minChars = 1;
            else
                previous = $(this).val().length;
        });

        $('#chatSubmit').click(function() {
            awesomeplete.minChars = 1;
        });
    });
</script>