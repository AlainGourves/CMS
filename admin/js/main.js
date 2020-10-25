let myForm = document.querySelector('form');
let inputFile = document.querySelector('.inputFile');

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
                if (fields[i].nodeName === "INPUT" && fields[i].type === "file") {
                    let label = fields[i].nextElementSibling;
                    let val = label.dataset.val;
                    label.lastChild.textContent = val;
                }
            }
        });
    }

    if (inputFile){
        inputFile.addEventListener('change', e => {
            let path = e.target.value;
            let file = path.split('\\').pop();
            let label = inputFile.nextElementSibling;
            label.lastChild.textContent = file;
        });
    }
});
