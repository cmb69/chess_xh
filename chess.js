function serialize(params) {
    function buildParam(name) {
        return name + "=" + encodeURIComponent(params[name]);
    }
    return Object.keys(params).map(buildParam).join("&");
}

function getParams(button) {
    var form = button.form;
    return serialize({
        // selected: form.selected.value,
        chess_game: form.chess_game.value,
        chess_ply: form.chess_ply.value,
        chess_flipped: form.chess_flipped.value,
        chess_action: button.value,
        chess_ajax: 1
    });
}

function onSubmit(event) {
    var target, form, request;

    function displayLoader() {
        var table, img;
        table = form.previousSibling;
        if (!table.querySelector(".chess_loader")) {
            img = document.createElement("img");
            img.src = "./plugins/chess/images/ajax-loader.gif";
            img.className = "chess_loader";
            table.appendChild(img);
        }
    }

    function isSuccess() {
        return request.status === 200 &&
            /<div id="chess_view_/.test(request.responseText) &&
            /<\/div>\s*$/.test(request.responseText);
    }

    function onSuccess(form, html) {
        var container = form.parentNode.parentNode;

        function removeMoveClasses() {
            var elements = container.querySelectorAll(".chess_move");
            elements.forEach(element => {
                element.className = "";
            });
        }

        container.innerHTML = html;
        form = container.querySelector(".chess_control_panel");
        form.addEventListener("click", onSubmit);
        setTimeout(removeMoveClasses, 1000);
    }

    function onReadyStateChange() {
        if (request.readyState === 4) {
            if (isSuccess()) {
                onSuccess(form, request.responseText);
            } else {
                form.removeEventListener("click", onSubmit);
            }
        }
    }

    target = event.target;
    if (target.nodeName === "BUTTON" && !target.disabled) {
        form = target.form;
        request = new XMLHttpRequest();
        request.open("GET", location.pathname + "?" + form.selected.value + "&" + getParams(target));
        request.onreadystatechange = onReadyStateChange;
        request.send();
        event.preventDefault();
        displayLoader();
    }
}

var forms = document.querySelectorAll(".chess_control_panel");
forms.forEach(form => {
    form.addEventListener("click", onSubmit);
});
