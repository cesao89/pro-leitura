var authActionFrom;
var authActionPermission;

function authActionBox(title, permission, form){
   $('#authActionModal').modal('show');
   $('#authActionTitle').html(title);

   authActionPermission = permission;
   authActionFrom = form;
}

function authActionBoxInbound(form){
   processModal('show', 'Finalizando compra ...');
   form.submit();
}

function authActionButton() {
   user = $('#authActionUser').val();
   password = $('#authActionPassword').val();
   $('#authActionButton').html('Autorizando ...');
   $('#authActionButton').attr("disabled", true);
   if (user != '' && password != '') {
      $.ajax({
         type: "post",
         url: baseUrl + "/auth/action/",
         dataType: "json",
         data: { authActionUser: user, authActionPassword: password, authActionPermission: authActionPermission }
         }).done(function( data ) {
            if (data.retorno == true){
               if (authActionFrom){
                  authActionFrom.submit();
               }
            } else {
               $('#authActionButton').html('Autorizar');
               $('#authActionButton').removeAttr("disabled");
               $('#authActionError').html(data.msg);
               $('#authActionError').css("display", "block");
            }
      });
   } else {
      $('#authActionError').html('Preencha o login e senha.');
      $('#authActionError').css("display", "block");
      $('#authActionButton').html('Autorizar');
      $('#authActionButton').removeAttr("disabled");
   }

}