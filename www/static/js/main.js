$(function() {
  $(loginUser).submit(function(e) {
    var isValid = true;
    if (user.value.length == 0) {
      isValid = false;
      alert("El campo de usuario no puede estar vacio");
    }
    if (pass.value.length == 0) {
      isValid = false;
      alert("El campo de la contraseña no puede estar vacio");
    }
    return isValid;
  });
});
