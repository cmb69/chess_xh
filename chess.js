if (window.addEventListener) {
    window.addEventListener("load", function () {
        "use strict";

        function serialize(params) {
            function buildParam(name) {
                return name + "=" + encodeURIComponent(params[name]);
            }
            return Object.keys(params).map(buildParam).join("&");
        }

        function getParams(button) {
            var form = button.form;
            return serialize({
                selected: form.selected.value,
                chess_game: form.chess_game.value,
                chess_ply: form.chess_ply.value,
                chess_flipped: form.chess_flipped.value,
                chess_action: button.value,
                chess_ajax: 1
            });
        }

        function onSubmit(event) {
            var target, form, request, method;

            function onSuccess(form, html) {
                var container = form.parentNode.parentNode;
                container.innerHTML = html;
                form = container.querySelector(".chess_control_panel");
                form.addEventListener("click", onSubmit);
            }

            function onReadyStateChange() {
                if (request.readyState === 4) {
                    if (request.status === 200) {
                        onSuccess(form, request.responseText);
                    } else {
                        form.removeEventListener("click", onSubmit);
                    }
                }
            }

            function getUrl() {
                var result = location.pathname;
                if (method === "GET") {
                    result += "?" + getParams(target);
                }
                return result;
            }

            function getBody() {
                var result = null;
                if (method !== "GET") {
                    result = getParams(target);
                }
                return result;
            }

            target = event.target;
            if (target.nodeName === "BUTTON") {
                form = target.form;
                method = form.method.toUpperCase();
                request = new XMLHttpRequest();
                request.open(method, getUrl());
                if (method === "POST") {
                    request.setRequestHeader("Content-Type",
                            "application/x-www-form-urlencoded");
                }
                request.onreadystatechange = onReadyStateChange;
                request.send(getBody());
                event.preventDefault();
            }
        }

        function registerClickHandlers() {
            var forms = document.getElementsByClassName("chess_control_panel");
            Array.prototype.forEach.call(forms, function (form) {
                form.addEventListener("click", onSubmit);
            });

        }

        registerClickHandlers();

    });
}
