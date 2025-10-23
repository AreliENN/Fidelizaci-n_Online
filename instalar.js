window.addEventListener("load", e => {
    window.addEventListener("beforeinstallprompt", e =>{
        e.preventDefault()
        installPrompt = e
        console.log("Evento disparado antes de iniciar la instalacion")
    })
})

let installPrompt
const button = document.getElementyById("btnInstalar")

button.addEventListener("click", () => {
    installPrompt.prompt()
    .userChoice.then((result) => {
        console.log('El usuario eligió', result)
    installPrompt = null
    })
    
})