window.addEventListener("load", function () {
    "use strict";

    function serialize(params) {
        var result = [], prop;
        for (prop in params) {
            if (params.hasOwnProperty(prop)) {
                result.push(prop + "=" + encodeURIComponent(params[prop]));
            }
        }
        return result.join("&");
    }

    function getParams(button) {
        var form = button.form,
            params = {
                selected: form.selected.value,
                chess_ply: (button.name === "chess_ply")
                        ? button.value
                        : form.querySelector("input[name=chess_ply]").value,
                chess_flip: (button.name === "chess_flip")
                    ? button.value
                    : form.querySelector("input[name=chess_flip]").value,
                chess_ajax: 1
            };
        return serialize(params);
    }

    function onSubmit(event) {
        var target = event.target, form, request;
        if (target.nodeName === "BUTTON") {
            form = target.form;
            request = new XMLHttpRequest();
            request.open("GET", location.pathname + "?" + getParams(target));
            request.onreadystatechange = function () {
                var container;
                if (request.readyState === 4) {
                    if (request.status === 200) {
                        container = form.parentNode.parentNode;
                        container.innerHTML = request.responseText;
                        form = container.querySelector(".chess_control_panel");
                        form.addEventListener("click", onSubmit);
                    } else {
                        form.removeEventListener("click", onSubmit);
                    }
                }
            };
            request.send(null);
            event.preventDefault();
        }
    }

    var forms = document.getElementsByClassName("chess_control_panel");
    Array.prototype.forEach.call(forms, function (form) {
        form.addEventListener("click", onSubmit);
    });
});
