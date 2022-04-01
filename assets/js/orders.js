function startIndividualCheck( orderId, email, adminAjaxUrl ) {
  var check = confirm( "Realmente deseja inicializar essa consulta?" );

  if ( ! check ) return;

  var outerHTMLBckp = jQuery( "a[data-autentify-score-email='" + email +"']" )[0].outerHTML;
  updateBeforeIndividual( email );

  jQuery( "#autentify-notice" ).remove();
  jQuery.ajax( {
    url : adminAjaxUrl,
    type : 'POST',
    data : {
      action : 'autentify_autenti_mail_post',
      param1 : orderId,
      param2 : email,
    },
    success : function( response ) {
      console.log("response", response);

      response = JSON.parse( response );
      var success = response['success'] == true;
      if ( success ) {
        updateAfterIndividualCheckSuccess( response['autenti_mail'] );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-success"><p>' + response['message'] + '</p></div>' );
      } else {
        updateAfterIndividualCheckFail( email, outerHTMLBckp );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-error"><p>' + response['message'] + '</p></div>' );
      }
    }
  } );
}

function updateBeforeIndividual( email ) {
  jQuery( "a[data-autentify-score-email='" + email + "']" ).each( function() {
    jQuery( this ).replaceWith( "<span data-autentify-score-email='" + email + "'>Consultando...</span>" );
  });
}

function updateAfterIndividualCheckFail( email, outerHTMLBckp ) {
  jQuery( "span[data-autentify-score-email='" + email + "']" ).each( function() {
    jQuery( this ).replaceWith( outerHTMLBckp );
  });
}

function updateAfterIndividualCheckSuccess( autentiMail ) {
  jQuery( "span[data-autentify-score-email='" + autentiMail["email"] + "']" ).each( function() {
    jQuery( this ).replaceWith( autentiMail["risk_score_html"] );
  });
  jQuery( "span[data-autentify-score-msg-email='" +  autentiMail["email"] + "']" ).each( function() {
    jQuery( this ).replaceWith( autentiMail["risk_score_msg_pt_br"] );
  });
}