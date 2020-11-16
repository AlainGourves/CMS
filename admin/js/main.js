const myForm = document.querySelector('form');
const inputFile = document.querySelector('.inputFile');
const alerte = document.querySelector(".alerte");
const configBtn = document.querySelector("#config");
const config = document.querySelector("#color_choice");


window.addEventListener("load", e => {
    if(configBtn){
        configBtn.addEventListener("click", e => {
            config.classList.toggle('open');
            e.preventDefault();
        });
        config.querySelector("span").addEventListener("click", e => {
            config.classList.remove('open');
        });
    }
    
    if (myForm) {
        const fields = myForm.elements;
        const emptyBtn = document.querySelector('#videForm');

        if(emptyBtn){
            emptyBtn.addEventListener("click", e => {
                for (i = 0; i < fields.length; i++) {
                    if (fields[i].nodeName === "SELECT"){
                        fields[i].selectedIndex = 0;
                    }
                    if (fields[i].nodeName === "INPUT" && (fields[i].type === "text" || fields[i].type === "number" || fields[i].type === "password" || fields[i].type === "date")) {
                        fields[i].value = '';
                    }
                    if (fields[i].nodeName === "INPUT" && fields[i].type === "file") {
                        let label = fields[i].nextElementSibling;
                        let val = label.dataset.val;
                        label.lastChild.textContent = val;
                    }
                    if (fields[i].nodeName === "INPUT" && fields[i].type === "checkbox") {
                        fields[i].checked = false;
                    }
                }
            });
        }
    }

    if (inputFile){
        inputFile.addEventListener('change', e => {
            let path = e.target.value;
            let file = path.split('\\').pop();
            if(file.length>35){
                file = file.substring(0,10) + '...' + file.slice(-8);
            }
            let label = inputFile.nextElementSibling;
            label.lastChild.textContent = file;
        });
    }

    let d = document.querySelector('input[type=date]');
    if (d && d.value==''){
        let today = new Date().toISOString().substr(0,10);
        d.value = today;
    }

    if(alerte){
        if (!alerte.classList.contains("ouinon")){
            alerte.insertAdjacentHTML('beforeend', "<span class=\"dashicons dashicons-no-alt\"></span>");
            alerte.addEventListener("click", e =>{
                alerte.remove();
            })
        }
    }
});

