let myForm = document.querySelector('form');

window.addEventListener("load", e => {

    if (myForm) {
        let fields = myForm.elements;
        let emptyBtn = document.querySelector('#videForm');

        emptyBtn.addEventListener("click", e => {
            for (i = 0; i < fields.length; i++) {
                if (fields[i].nodeName === "SELECT"){
                    fields[i].selectedIndex = 0;
                }
                if (fields[i].nodeName === "INPUT" && (fields[i].type === "text" || fields[i].type === "number" || fields[i].type === "password")) {
                    fields[i].value = '';
                }
            }
        });
    }
});