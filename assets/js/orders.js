function startIndividualCheck( orderId, adminAjaxUrl ) {
  var check = confirm( "Realmente deseja inicializar essa consulta?" );

  if ( ! check ) return;

  var orderTd = jQuery( `#post-${orderId} > td.column-autentify_autenti_mail_score` )[0];
  var oldInnerHTML = orderTd.innerHTML;
  updateBeforeIndividual( orderTd );

  jQuery( "#autentify-notice" ).remove();
  jQuery.ajax( {
    url : adminAjaxUrl,
    type : 'POST',
    data : {
      action : 'autentify_autenti_mail_post',
      param1 : orderId
    },
    success : function( response ) {
      response = JSON.parse( response );
      var success = response['success'] == true;
      if ( success ) {
        updateAfterIndividualCheckSuccess( orderId, orderTd, response['autenti_mail'] );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-success"><p>' + response['message'] + '</p></div>' );
      } else {
        updateAfterIndividualCheckFail( orderTd, oldInnerHTML );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-error"><p>' + response['message'] + '</p></div>' );
      }
    }
  } );
}

function updateBeforeIndividual( orderTd ) {
  orderTd.innerHTML = "Consultando...";
}

function updateAfterIndividualCheckFail( orderTd, oldInnerHTML ) {
  orderTd.innerHTML = oldInnerHTML;
}

function updateAfterIndividualCheckSuccess( orderId, orderTd, autentiMail ) {
  orderTd.innerHTML = autentiMail["risk_score_html"];
  jQuery( `#post-${orderId} > td.column-autentify_autenti_mail_score_msg` )[0].innerHTML = autentiMail["risk_score_msg_pt_br"];
}