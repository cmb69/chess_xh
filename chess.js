if (typeof window.addEventListener === "function") {
    window.addEventListener("load", function () {
        "use strict";

        function serialize(params) {
            function buildParam(name) {
                return name + "=" + encodeURIComponent(params[name]);
            }
            return Object.keys(params).map(buildParam).join("&");
        }

        function getParams(button) {
            var form = button.form,
                params = {
                    selected: form.selected.value,
                    chess_game: form.chess_game.value,
                    chess_ply: form.chess_ply.value,
                    chess_flipped: form.chess_flipped.value,
                    chess_action: button.value,
                    chess_ajax: 1
                };
            return serialize(params);
        }


        function onSubmit(event) {
            function onSuccess(form, html) {
                var container = form.parentNode.parentNode;
                container.innerHTML = html;
                form = container.querySelector(".chess_control_panel");
                form.addEventListener("click", onSubmit);
            }
            var target = event.target, form, request, method, url, body;
            if (target.nodeName === "BUTTON") {
                form = target.form;
                method = form.method.toUpperCase();
                url = location.pathname;
                if (method === "GET") {
                    url += "?" + getParams(target);
                } else {
                    body = getParams(target);
                }
                request = new XMLHttpRequest();
                request.open(method, url);
                if (method === "POST") {
                    request.setRequestHeader("Content-Type",
                            "application/x-www-form-urlencoded");
                }
                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status === 200) {
                            onSuccess(form, request.responseText);
                        } else {
                            form.removeEventListener("click", onSubmit);
                        }
                    }
                };
                request.send(body);
                event.preventDefault();
            }
        }

        var forms = document.getElementsByClassName("chess_control_panel");
        Array.prototype.forEach.call(forms, function (form) {
            form.addEventListener("click", onSubmit);
        });
    });
}
