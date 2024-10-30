function startIndividualCheck( orderId, adminAjaxUrl ) {
  var check = confirm( "Realmente deseja inicializar essa consulta?" );

  if ( ! check ) return;

  var orderScoreTd = jQuery( `#post-${orderId} > td.column-autentify_autenti_mail_score` )[0];
  var scoreTdOldInnerHTML = orderScoreTd.innerHTML;
  var orderScoreMsgTd = jQuery( `#post-${orderId} > td.column-autentify_autenti_mail_score_msg` )[0];
  var scoreMsgTdOldInnerHTML = orderScoreMsgTd.innerHTML;
  updateBeforeIndividual( orderScoreTd, orderScoreMsgTd );

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
        updateAfterIndividualCheckSuccess( orderScoreTd, orderScoreMsgTd, response['autenti_mail'] );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-success"><p>' + response['message'] + '</p></div>' );
      } else {
        updateAfterIndividualCheckFail( orderScoreTd, scoreTdOldInnerHTML, orderScoreMsgTd, scoreMsgTdOldInnerHTML );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-error"><p>' + response['message'] + '</p></div>' );
      }
    }
  } );
}

function updateBeforeIndividual( orderScoreTd, orderScoreMsgTd ) {
  orderScoreTd.innerHTML = "Consultando...";
  orderScoreMsgTd.innerHTML = '<div class="autentify-analysis-status"><span>Aguarde</span></div>';
}

function updateAfterIndividualCheckFail( orderScoreTd, scoreTdOldInnerHTML, orderScoreMsgTd, scoreMsgTdOldInnerHTML ) {
  orderScoreTd.innerHTML = scoreTdOldInnerHTML;
  orderScoreMsgTd.innerHTML = scoreMsgTdOldInnerHTML;
}

function updateAfterIndividualCheckSuccess( orderScoreTd, orderScoreMsgTd, autentiMail ) {
  orderScoreTd.innerHTML = autentiMail["risk_score_html"];
  orderScoreMsgTd.innerHTML = autentiMail["risk_score_msg_pt_br"];
}