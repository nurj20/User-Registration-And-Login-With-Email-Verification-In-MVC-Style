const login = document.querySelector('.login')
const register = document.querySelector('.register')
const loginDiv = document.querySelector('.login-section')
const registerDiv = document.querySelector('.register-section')
const registerForm = document.querySelector('.register-form')
const loginForm = document.querySelector('.login-form')
const loginSubmit = document.querySelector('.login-submit')
const registerSubmit = document.querySelector('.register-submit')
const msgSection = document.querySelector('.message-section')

login.addEventListener('click', ()=>{
loginDiv.style.display='block'
registerDiv.style.display="none"
msgSection.style.display="none"
})
register.addEventListener('click', ()=>{
    loginDiv.style.display='none'
    registerDiv.style.display="block"
    msgSection.style.display="none"
})

loginSubmit.addEventListener('click', (e)=>{
    e.preventDefault()
    const formData = new FormData(loginForm)
// for(let [name, value] of formData) {
//     alert(`${name} = ${value}`); // key1 = value1, then key2 = value2
//   }
    fetch('/controller.php', {
        method:'POST',
        body:formData
    })
    .then(res=>res.json())
    .then(data=>{
        msgSection.style.display="block"
        loginDiv.style.display='none'
registerDiv.style.display="none"
        msgSection.innerHTML=`<div>${data.status}</div>`
    })
})
registerSubmit.addEventListener('click', (e)=>{
    e.preventDefault()
    fetch('/controller.php', {
        method:'POST',
        body:new FormData(registerForm)
    })
    .then(res=>res.json())
    .then(data=>{msgSection.style.display="block"
    loginDiv.style.display='none'
registerDiv.style.display="none"
    msgSection.innerHTML=`<div>${data.status}</div>`
console.log(data.status)
})   
})