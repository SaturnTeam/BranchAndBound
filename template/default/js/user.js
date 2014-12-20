$('#choseUser').change(function (event)
{
    // Stop form from submitting normally
    event.preventDefault();
// Get some values from elements on the page:
    var $form = $("#choseUserForm"),
            id = $form.find("#choseUser").val(),
            url = $form.attr("action");
// Send the data using post
    var posting = $.post(url, {getgroup: id});
    console.log(url);
// Put the results in a div
    posting.done(function (data) {
        console.log(data);
        if (data)
        {
            var values = data.split(';');
            $("#groupmulti").val(values).trigger("chosen:updated");
        }
        else
        {
            $("#groupmulti").val(0).trigger("chosen:updated");
        }
    });
}
);
$("#saveUserGroups").click(function (event)
{
    event.preventDefault();
    var $form = $("#saveUserGroupsForm"),
            groups = $form.find("#groupmulti").val(),
            url = $form.attr("action"),
            id = $("#choseUserForm").find("#choseUser").val();
// Send the data using post
    var posting = $.post(url, {updateGroups: 0, userid: id, groups: groups});
// Put the results in a div
    posting.done(function (data) {
        console.log(data);
        if(data)
        {
            $("#saveUserGroups").text("Сохранено");
            setTimeout(function(){$("#saveUserGroups").text("Сохранить изменения");}, 1000);
        }
        else
        {
            $("#saveUserGroups").text("Ошибка");
        }
    });
}
);
$('#groupList').change(function (event)
{
    console.log($(this).val());
    // Stop form from submitting normally
    event.preventDefault();
// Get some values from elements on the page:
    var $form = $("#groupListForm"),
            id = $form.find("select").val(),
            url = $form.attr("action");
// Send the data using post
    var posting = $.post(url, {getDescription: id});
// Put the results in a div
    posting.done(function (data) {
        console.log(data);
        if (data)
        {
            $("#description").val(data);
        }
        else
        {

        }
    });
}
);
var Captcha = {
    captchaID: null,
    userID: null,
    getNew: function (div) {
        var url = 'ajax.php?newCaptcha=0';
        $.ajax(
                {
                    url: url,
                    dataType: "html",
                    timeout: 20000,
                    success: function (data)
                    {
                        console.log(data);
                        Captcha.updateDiv(data, div);
                    }
                });
    },
    checkUser: function (id, div) {
        var url = 'ajax.php?isrequiredCaptcha=' + id;
        $.ajax(
                {
                    url: url,
                    dataType: "html",
                    timeout: 2000,
                    success: function (data)
                    {
                        console.log(url);
                        console.log(data);
                        if (data !== '')
                        {
                            Captcha.updateDiv(data, div);
                        }
                        else
                        {
                            $(div).html('');
                        }

                        //console.log(div);
                        /*Key = data;
                         Key = '<div class="captcha_img"><img src="' + window.location.protocol + '//i.captcha.yandex.net/image?key=' + Key + '" onclick="GetCaptcha(\'#captcha_div\');"></div><input type="hidden" name="captcha_id" value="' + Key + '"><input type="text" class="form-control" autocomplete="off" name="captcha_value" placeholder="Капча" requed>';
                         $(CaptchaDiv).html(Key);*/
                    }
                });
    },
    updateDiv: function (data, div) {
        data = '<div class="captcha_img">\n\
                                <img src="'
                + window.location.protocol
                + '//i.captcha.yandex.net/image?key='
                + data
                + '" onclick="getCaptcha(\'' + div + '\');">\n\
                                </div>\n\
                                <input type="hidden" name="captcha_id" value="'
                + data
                + '">\n\
                                <input type="text" class="form-control" autocomplete="off" name="captcha_value" placeholder="Капча" requed>';
        $(div).html(data);
    }
};
function DeleteFile(id, object)
{
    var Key = '';
    var Url = 'deletefile.php?delete=' + id;
    $.ajax(
            {
                url: Url,
                dataType: "html",
                timeout: 2000,
                async: true,
                success: function (data)
                {
                    console.log(data);
                    $(object).html(data);
                    $(object).toggleClass("delete deleted")
                }
            });
}
$(document).ready(function () {
    // инициализация кнопок и добавление функций на событие нажатия
    $("#create_reg").click(function () {
        var create_dialog = $("#dialog_window_1");
        var create_button = $(this);
        // если окно уже открыто, то закрыть его и сменить надпись кнопки
        if (create_dialog.dialog("isOpen")) {
            create_button.button("option", "label", "Создать новое окно");
            create_dialog.dialog("close");
        } else {
            Captcha.getNew('#captcha_div1');
            create_button.button("option", "label", "Закрыть окно");
            create_dialog.dialog("open");
        }
    });
    $("#create_sgn").button().click(function () {
        var create_dialog = $("#dialog_window_2");
        var create_button = $(this);

        // если окно уже открыто, то закрыть его и сменить надпись кнопки
        if (create_dialog.dialog("isOpen")) {
            create_button.button("option", "label", "Создать новое окно");
            create_dialog.dialog("close");
        } else {
            create_button.button("option", "label", "Закрыть окно");
            create_dialog.dialog("open");
        }
    });
    // autoOpen : false – означает, что окно проинициализируется но автоматически открыто не будет
    $("#dialog_window_1").dialog({
        width: "auto",
        height: "auto",
        autoOpen: false
    });
    $("#dialog_window_2").dialog({
        width: "auto",
        height: "auto",
        autoOpen: false
    });

});
$(document).ready(function () {
    $(".delete").click(function () {
        //$(this).click(DeleteFile($(this).attr("value"),this));
        DeleteFile($(this).attr("value"), this);
        //console.log($(this).attr("value"));
    });
    $("#captcha_div1>img").click(function () {
       Captcha.getNew("#captcha_div1");
    });
    $("#captcha_div3>img").click(function () {
        Captcha.getNew("#captcha_div3");
    });
    $("#captcha_div2>img").click(function () {
       Captcha.getNew("#captcha_div2");
    });
    $('#dialog_window_2 > form:nth-child(1) > input:nth-child(1)').focusout(function(){
        Captcha.checkUser($(this).val(), "#captcha_div2");
    });
});
$('#submit').click(function () {
    if ($('input[type="file"]').val() != '')
        $('#submit').text('Загружается...').toggleClass('btn-success btn-primary');
});
Captcha.getNew("#captcha_div3");