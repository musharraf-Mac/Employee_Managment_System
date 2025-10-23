setTimeout(() => {
  document.getElementById('details').classList.remove('hide_b');
  document.getElementById('login_btn').classList.remove('hide_b');
  document.getElementById('reg_btn').classList.remove('hide_b');
}, 1000); // 1000 milliseconds = 1 second
function detail() { alert('Employee details'); 
    document.location.href="E_details.html";
}
function login() {
    document.location.href="login.html";
}
function Register() {
    document.location.href="register.html";
}
