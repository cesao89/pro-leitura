/* 
 * Common
 */
$(function()
{
  // Ativa popovers
  $("[rel^='popover']").popover({
    placement: 'left',
    trigger: 'hover'
  });
});

function Trim(strTexto)
{
  return strTexto.replace(/^\s+|\s+$/g, '');
}

/**
 * Preload de imagens do sistema
 */
function preloader() 
{
 
  // counter
  var i = 0;
 
  // create object
  imageObj = new Image();
 
  // set image list
  images = new Array();
  images[0]= baseUrl + "/images/ajax-loader.gif"
 
  // start preloading
  for(i=0; i<images.length; i++) 
  {
    imageObj.src=images[i];
  }
 
} 
 
/**
  * Modal de processos
  * (especificação do Bootstrap modal)
  * 
  * @param string action 'show' ou 'hide' 
  * @param string h3 Valor do H3 (opcional)
  * 
  * @example processModal('show');
  * @example processModal('show', 'teste');
  * @example processModal('hide');
  */
function processModal(action, h3){
  
  if(h3!=undefined && h3!=''){
    $('#h3Modal').html(h3);
  }
  
  $('#processAjax').modal(action);
}


function TestaCPF(strCPF) {
  var Soma;
  var Resto;
  Soma = 0;
  
  strCPFparaValidar = strCPF.replace(/\./g, "");
  strCPFparaValidar = strCPFparaValidar.replace(/\-/g, "");
  
  if (strCPFparaValidar == "00000000000"){
    resultadoFalhaCPF(strCPF);
    return false;
  }
     
  for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPFparaValidar.substring(i-1, i)) * (11 - i);
  Resto = (Soma * 10) % 11;
     
  if ((Resto == 10) || (Resto == 11))  Resto = 0;
  if (Resto != parseInt(strCPFparaValidar.substring(9, 10)) ){
    resultadoFalhaCPF(strCPF);
    return false;
  }
     
  Soma = 0;
  for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPFparaValidar.substring(i-1, i)) * (12 - i);
  Resto = (Soma * 10) % 11;
     
  if ((Resto == 10) || (Resto == 11))  Resto = 0;
  
  if (Resto != parseInt(strCPFparaValidar.substring(10, 11) ) ){
    resultadoFalhaCPF(strCPF);
    return false;
  }
  return true;
}

function resultadoFalhaCPF(strCPF){
  processModal('hide');
  $('#alertModal div h3').html('CPF informado não é válido');
  $('#alertModal .modal-body div').html('O CPF <b>' + strCPF + '</b> não é válido. Verifique se digitou corretamente.');
  $('#alertModal').modal('show');
}


function toggle(x,origColor){
  x.style.backgroundColor = origColor
}

function calculaIdade(dataNasc){ 
   var dataAtual = new Date();
   var anoAtual = dataAtual.getFullYear();
   var anoNascParts = dataNasc.split('/');
   var diaNasc =anoNascParts[0];
   var mesNasc =anoNascParts[1];
   var anoNasc =anoNascParts[2];
   var idade = anoAtual - anoNasc;
   var mesAtual = dataAtual.getMonth() + 1; 
   //se mês atual for menor que o nascimento, nao fez aniversario ainda; (26/10/2009) 
   if(mesAtual < mesNasc){
      idade--; 
   }else {
      //se estiver no mes do nasc, verificar o dia
      if(mesAtual == mesNasc){ 
         if(dataAtual.getDate() < diaNasc ){ 
            //se a data atual for menor que o dia de nascimento ele ainda nao fez aniversario
            idade--; 
         }
      }
   } 
   return idade; 
}

function resultadoFalhaBirthDate(){
  processModal('hide');
  $('#alertModal div h3').html('Restrição de idade');
  $('#alertModal .modal-body div').html('A adesão a este seguro é válida para pessoas com idade entre 18 e 65 anos. <br /><br /> <b> - Confirme a data de nascimento para não perder o cliente.</b> <br><div class="alert alert-success"><h4>Script</h4>Senhor, infelizmente a adesão ao seguro esta limitado a 65 anos. <br>Não poderemos ofertar o produto.<br> Agradecemos pela atenção!<br> Posso lhe ajudar em algo mais?</div>');
  $('#alertModal').modal('show');
}

function resultadoFalhaBirthDate1(){
  processModal('hide');
  $('#alertModal div h3').html('Restrição de idade');
  $('#alertModal .modal-body div').html('A adesão a este seguro é válida para pessoas com idade superior a 18 anos. <br /><br /> <b> - Confirme a data de nascimento para não perder o cliente.</b> <br><div class="alert alert-success"><h4>Script</h4>Senhor, infelizmente a adesão ao seguro esta limitado maior idade. <br>Não poderemos ofertar o produto.<br> Agradecemos pela atenção!<br> Posso lhe ajudar em algo mais?</div>');
  $('#alertModal').modal('show');
}

function resultadoFalhaEmail(){
  processModal('hide');
  $('#alertModal div h3').html('Erro');
  $('#alertModal .modal-body div').html('O email informado é inválido.');
  $('#alertModal').modal('show');
}

function scriptBox(title, content){
  processModal('hide');
  $('#alertModal div h3').html(title);
  $('#alertModal .modal-body div').html('<div class="alert-success" style="font-size=16px;text-align:left;padding: 10px"><h4>Script</h4><br>' + content + '</div>');
  $('#alertModal').modal('show');
}

function infoBox(title, content){
  processModal('hide');
  $('#alertModal div h3').html(title);
  $('#alertModal .modal-body div').html('<div class="alert alert-error" style="font-size=16px;text-align:left;padding-bottom:30px"><br>' + content + '</div>');
  $('#alertModal').modal('show');
}

new function($) {
   $.fn.setCursorPosition = function(pos) {
     if ($(this).get(0).setSelectionRange) {
       $(this).get(0).setSelectionRange(pos, pos);
     } else if ($(this).get(0).createTextRange) {
       var range = $(this).get(0).createTextRange();
       range.collapse(true);
       range.moveEnd('character', pos);
       range.moveStart('character', pos);
       range.select();
     }
   }
 }(jQuery);