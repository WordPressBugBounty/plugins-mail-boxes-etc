if (typeof mbeButtonAction !== "function") {
    function mbeButtonAction(button, url, target, confirmation, confirmationText, parameters, lock) {
        let ok = false;
        if (confirmation === 'true') {
            if (confirm(confirmationText) === true) {
                ok = true;
            }
        } else {
            ok = true;
        }
        if (ok === true) {
            if (lock === 'true' && undefined !== button) {
                button.disabled = true;
            }
            let a = document.createElement('a');
            a.target = target;
            // let parameters = parameters
            let queryString = '';
            if (undefined !== parameters && parameters.length) {
                parameters = JSON.parse(parameters)
                Object.entries(parameters).forEach((entry) => {
                    const [key, value] = entry;
                    parameters[key] = document.getElementById(key).value;
                });
                queryString = '&' + (new URLSearchParams(parameters).toString())
            }
            a.href = url + queryString;
            a.click();
        }
    }
}