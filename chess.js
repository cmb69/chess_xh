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
            function getValue(form, param) {
                var result, selector;
                if (button.name === "chess_" + param) {
                    result = button.value;
                } else {
                    selector = "input[name=chess_" + param + "]";
                    result = form.querySelector(selector).value;
                }
                return result;
            }
            var form = button.form,
                params = {
                    selected: form.selected.value,
                    chess_ply: getValue(form, "ply"),
                    chess_flip: getValue(form, "flip"),
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
            var target = event.target, form, request;
            if (target.nodeName === "BUTTON") {
                form = target.form;
                request = new XMLHttpRequest();
                request.open("GET",
                        location.pathname + "?" + getParams(target));
                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status === 200) {
                            onSuccess(form, request.responseText);
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
}
